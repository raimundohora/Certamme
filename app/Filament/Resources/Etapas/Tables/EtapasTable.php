<?php

namespace App\Filament\Resources\Etapas\Tables;

use App\Models\Etapa;
use App\Enums\TipoEtapa;
use App\Services\ApuracaoService;
use App\Services\TitulosService;
use App\Services\ConvocacaoService;
use App\Services\DiscursivaService;
use App\Services\EnsalamentoService;
use App\Services\ImportarNotasService;
use App\Services\KitSalaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EtapasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ordem')->label('Ordem')->searchable()->sortable(),
                TextColumn::make('concurso.nome')->label('Concurso')->searchable()->sortable(),
                TextColumn::make('cargo.nome')->label('Cargo')->searchable()->sortable(),
                TextColumn::make('tipo')->label('Tipo')->badge()->sortable(),
                IconColumn::make('eliminatoria')->label('Eliminatoria')->boolean(),
                IconColumn::make('classificatoria')->label('Classificatoria')->boolean(),
                TextColumn::make('nota_minima')->label('Nota minima')->sortable()->toggleable(),
                TextColumn::make('peso')->label('Peso')->sortable()->toggleable(),
                TextColumn::make('notas_count')->label('Notas')->counts('notas')->toggleable(),
                TextColumn::make('resultados_count')->label('Resultados')->counts('resultados')->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('importar_notas')
                    ->label('Importar notas')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->modalHeading('Importar notas (CSV)')
                    ->modalSubmitActionLabel('Importar')
                    ->schema([
                        Placeholder::make('formato')
                            ->label('Formato esperado')
                            ->content(new HtmlString(
                                'Planilha CSV com cabeçalho. Colunas aceitas:<br>'
                                .'<code>numero_inscricao</code> (obrigatória), '
                                .'<code>disciplina</code> (opcional), '
                                .'<code>acertos</code> e/ou <code>nota</code>.<br>'
                                .'Separador <code>,</code> ou <code>;</code>; nota pode usar vírgula decimal. '
                                .'Se a linha não tiver <code>nota</code>, usa os <code>acertos</code>.'
                            )),
                        FileUpload::make('arquivo')
                            ->label('Arquivo CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/csv'])
                            ->storeFiles(false)
                            ->required(),
                        TextInput::make('disciplina_padrao')
                            ->label('Disciplina padrão (opcional)')
                            ->helperText('Aplicada às linhas sem coluna "disciplina". Deixe em branco para nota geral da etapa.'),
                    ])
                    ->action(function (Etapa $record, array $data) {
                        $file = $data['arquivo'];
                        if (is_array($file)) {
                            $file = reset($file);
                        }
                        $conteudo = @file_get_contents($file->getRealPath());
                        if ($conteudo === false || $conteudo === '') {
                            Notification::make()->title('Não consegui ler o arquivo')->danger()->send();
                            return;
                        }

                        $r = app(ImportarNotasService::class)->importar(
                            $record,
                            $conteudo,
                            $data['disciplina_padrao'] ?: null,
                        );

                        $corpo = "Criadas: {$r['criadas']} · Atualizadas: {$r['atualizadas']} · Ignoradas: {$r['ignoradas']} (de {$r['linhas']} linhas)";
                        if (! empty($r['erros'])) {
                            $amostra = array_slice($r['erros'], 0, 5);
                            $corpo .= "\n\nProblemas:\n• ".implode("\n• ", $amostra);
                            if (count($r['erros']) > 5) {
                                $corpo .= "\n• (+".(count($r['erros']) - 5)." outros)";
                            }
                        }

                        Notification::make()
                            ->title($r['criadas'] + $r['atualizadas'] > 0 ? 'Notas importadas' : 'Nada importado')
                            ->body($corpo)
                            ->color(empty($r['erros']) ? 'success' : 'warning')
                            ->persistent()
                            ->send();
                    }),
                Action::make('apurar')
                    ->label('Apurar resultado')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Soma as notas desta etapa, aplica a nota mínima/eliminação e gera a classificação (grava em Resultados). Pode rodar de novo quando quiser.')
                    ->action(function (Etapa $record) {
                        $r = app(ApuracaoService::class)->apurarEtapa($record);
                        Notification::make()
                            ->title('Apuração concluída')
                            ->body("Apurados: {$r['apurados']} · Eliminados: {$r['eliminados']} · Classificados: {$r['classificados']} · Aprovados (dentro das vagas): {$r['aprovados']}")
                            ->success()
                            ->send();
                    }),
                Action::make('ensalar')
                    ->label('Ensalar')
                    ->icon('heroicon-o-building-office-2')
                    ->color('gray')
                    ->modalHeading('Ensalamento (alocar em salas)')
                    ->modalSubmitActionLabel('Ensalar')
                    ->modalDescription('Distribui os candidatos nas salas do concurso respeitando a capacidade. Refaz a alocação desta etapa do zero.')
                    ->schema([
                        Select::make('ordem')
                            ->label('Ordem dos candidatos')
                            ->options([
                                'nome'             => 'Alfabética (nome)',
                                'numero_inscricao' => 'Nº de inscrição',
                            ])
                            ->default('nome')
                            ->required(),
                        Toggle::make('incluir_pendentes')
                            ->label('Incluir inscrições pendentes')
                            ->helperText('Por padrão aloca apenas as confirmadas.')
                            ->default(false),
                    ])
                    ->action(function (Etapa $record, array $data) {
                        $r = app(EnsalamentoService::class)->ensalar($record, [
                            'ordem'             => $data['ordem'] ?? 'nome',
                            'incluir_pendentes' => (bool) ($data['incluir_pendentes'] ?? false),
                        ]);
                        $corpo = "Alocados: {$r['alocados']} em {$r['salas_usadas']} sala(s) · Candidatos: {$r['candidatos']} · Capacidade: {$r['capacidade_total']}";
                        $semVaga = $r['sem_vaga'] > 0;
                        if ($semVaga) {
                            $corpo .= "\n\n⚠ {$r['sem_vaga']} sem vaga — capacidade insuficiente.";
                        }
                        Notification::make()
                            ->title($r['alocados'] > 0 ? 'Ensalamento concluído' : 'Nada alocado')
                            ->body($corpo)
                            ->color($semVaga ? 'warning' : ($r['alocados'] > 0 ? 'success' : 'danger'))
                            ->persistent()
                            ->send();
                    }),
                Action::make('convocar')
                    ->label('Convocar')
                    ->icon('heroicon-o-megaphone')
                    ->color('warning')
                    ->modalHeading('Convocar candidatos')
                    ->modalSubmitActionLabel('Convocar')
                    ->modalDescription('Gera convocações a partir do resultado desta etapa. Nascem não publicadas (publique depois na tela de Convocações). Refaz as convocações da etapa.')
                    ->schema([
                        Select::make('criterio')
                            ->label('Quem convocar')
                            ->options([
                                'aprovado'      => 'Somente aprovados (dentro das vagas)',
                                'classificavel' => 'Aprovados + classificáveis',
                            ])
                            ->default('aprovado')
                            ->required(),
                    ])
                    ->action(function (Etapa $record, array $data) {
                        $r = app(ConvocacaoService::class)->convocar($record, [
                            'criterio' => $data['criterio'] ?? 'aprovado',
                        ]);
                        Notification::make()
                            ->title($r['convocados'] > 0 ? 'Convocações geradas' : 'Ninguém convocado')
                            ->body("Convocados: {$r['convocados']} (critério: {$r['criterio']}). Publique na tela de Convocações.")
                            ->color($r['convocados'] > 0 ? 'success' : 'warning')
                            ->send();
                    }),
                Action::make('lista_presenca')
                    ->label('Lista de presença (PDF)')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->action(function (Etapa $record) {
                        $dados = app(KitSalaService::class)->dadosListaPresenca($record);
                        if (count($dados['salas']) === 0) {
                            Notification::make()
                                ->title('Sem ensalamento')
                                ->body('Rode o ensalamento antes de gerar a lista de presença.')
                                ->warning()->send();
                            return null;
                        }
                        $pdf = Pdf::loadView('pdf.lista-presenca', $dados)->setPaper('a4');
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'lista-presenca-etapa-' . $record->ordem . '.pdf',
                        );
                    }),
                Action::make('mapa_sala')
                    ->label('Mapa de sala (PDF)')
                    ->icon('heroicon-o-map')
                    ->color('gray')
                    ->action(function (Etapa $record) {
                        $dados = app(KitSalaService::class)->dadosMapaSala($record);
                        if (count($dados['salas']) === 0) {
                            Notification::make()->title('Sem ensalamento')->body('Rode o ensalamento antes de gerar o mapa de sala.')->warning()->send();
                            return null;
                        }
                        $pdf = Pdf::loadView('pdf.mapa-sala', $dados)->setPaper('a4', 'landscape');
                        return response()->streamDownload(fn () => print($pdf->output()), 'mapa-sala-etapa-' . $record->ordem . '.pdf');
                    }),
                Action::make('ata_sala')
                    ->label('Ata de sala (PDF)')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('gray')
                    ->action(function (Etapa $record) {
                        $dados = app(KitSalaService::class)->dadosAta($record);
                        if (count($dados['salas']) === 0) {
                            Notification::make()->title('Sem ensalamento')->body('Rode o ensalamento antes de gerar as atas.')->warning()->send();
                            return null;
                        }
                        $pdf = Pdf::loadView('pdf.ata-sala', $dados)->setPaper('a4');
                        return response()->streamDownload(fn () => print($pdf->output()), 'ata-sala-etapa-' . $record->ordem . '.pdf');
                    }),
                Action::make('cartoes_resposta')
                    ->label('Cartões-resposta (PDF)')
                    ->icon('heroicon-o-rectangle-group')
                    ->color('gray')
                    ->modalHeading('Gerar cartões-resposta')
                    ->modalSubmitActionLabel('Gerar PDF')
                    ->modalDescription('Um cartão por candidato alocado, na ordem sala → carteira.')
                    ->schema([
                        TextInput::make('questoes')
                            ->label('Nº de questões')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Deixe em branco para usar a qtd. de questões da prova cadastrada na etapa.'),
                        Select::make('alternativas')
                            ->label('Alternativas por questão')
                            ->options([4 => '4 (A–D)', 5 => '5 (A–E)'])
                            ->default(5)
                            ->required(),
                    ])
                    ->action(function (Etapa $record, array $data) {
                        $qtd = isset($data['questoes']) && $data['questoes'] !== null && $data['questoes'] !== ''
                            ? (int) $data['questoes'] : null;
                        $dados = app(KitSalaService::class)->dadosCartoes($record, $qtd, (int) ($data['alternativas'] ?? 5));
                        if (count($dados['candidatos']) === 0) {
                            Notification::make()->title('Sem ensalamento')->body('Rode o ensalamento antes de gerar os cartões.')->warning()->send();
                            return null;
                        }
                        if ($dados['qtdQuestoes'] === 0) {
                            Notification::make()->title('Nº de questões não definido')->body('Informe a quantidade de questões ou cadastre a prova (qtd_questoes).')->warning()->send();
                            return null;
                        }
                        $pdf = Pdf::loadView('pdf.cartoes-resposta', $dados)->setPaper('a4');
                        return response()->streamDownload(fn () => print($pdf->output()), 'cartoes-resposta-etapa-' . $record->ordem . '.pdf');
                    }),
                Action::make('pontuar_titulos')
                    ->label('Pontuar títulos')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (Etapa $record) => $record->tipo === TipoEtapa::TITULOS)
                    ->requiresConfirmation()
                    ->modalDescription('Converte os títulos avaliados em uma nota por candidato (respeitando o teto de cada tipo). Depois rode "Apurar resultado" para classificar.')
                    ->action(function (Etapa $record) {
                        $r = app(TitulosService::class)->gerarNotas($record);
                        if (! empty($r['sem_tipos'])) {
                            Notification::make()->title('Sem tipos de título')->body('Cadastre os tipos de título desta etapa antes de pontuar.')->warning()->send();
                            return;
                        }
                        Notification::make()
                            ->title($r['inscricoes'] > 0 ? 'Títulos pontuados' : 'Nada a pontuar')
                            ->body("Notas geradas: {$r['inscricoes']} (de {$r['titulos']} título(s), {$r['tipos']} tipo(s)). Agora rode Apurar resultado.")
                            ->color($r['inscricoes'] > 0 ? 'success' : 'warning')
                            ->send();
                    }),
                Action::make('gerar_folhas_discursiva')
                    ->label('Gerar folhas (discursiva)')
                    ->icon('heroicon-o-document-plus')
                    ->color('info')
                    ->visible(fn (Etapa $record) => $record->tipo === TipoEtapa::REDACAO)
                    ->requiresConfirmation()
                    ->modalDescription('Cria uma folha anônima (com código de controle) para cada inscrição confirmada desta etapa que ainda não tenha.')
                    ->action(function (Etapa $record) {
                        $r = app(DiscursivaService::class)->gerarFolhas($record);
                        Notification::make()
                            ->title($r['criadas'] > 0 ? 'Folhas geradas' : 'Nada a gerar')
                            ->body("Criadas: {$r['criadas']} · Já existiam: {$r['existentes']} · Total: {$r['total']}.")
                            ->color($r['criadas'] > 0 ? 'success' : 'gray')
                            ->send();
                    }),
                Action::make('consolidar_discursiva')
                    ->label('Consolidar discursiva')
                    ->icon('heroicon-o-scale')
                    ->color('success')
                    ->visible(fn (Etapa $record) => $record->tipo === TipoEtapa::REDACAO)
                    ->modalHeading('Consolidar correções da discursiva')
                    ->modalSubmitActionLabel('Consolidar')
                    ->modalDescription('Nota final = média das 2 correções mais próximas. Acima da discrepância vira "divergente" (pede 3ª correção). Gera as notas; depois rode Apurar.')
                    ->schema([
                        TextInput::make('discrepancia_max')
                            ->label('Discrepância máxima entre correções')
                            ->numeric()->step('0.01')->minValue(0)->default(2)
                            ->helperText('Diferença máxima tolerada entre as duas correções mais próximas.'),
                    ])
                    ->action(function (Etapa $record, array $data) {
                        $r = app(DiscursivaService::class)->consolidar($record, (float) ($data['discrepancia_max'] ?? 2));
                        Notification::make()
                            ->title('Consolidação concluída')
                            ->body("Corrigidas: {$r['corrigidas']} · Divergentes: {$r['divergentes']} · Pendentes: {$r['pendentes']} · Notas geradas: {$r['notas']} (de {$r['total']}).")
                            ->color($r['divergentes'] > 0 ? 'warning' : 'success')
                            ->persistent()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
