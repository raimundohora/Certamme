<?php

namespace App\Filament\Resources\Etapas\Tables;

use App\Models\Etapa;
use App\Services\ApuracaoService;
use App\Services\EnsalamentoService;
use App\Services\ImportarNotasService;
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
