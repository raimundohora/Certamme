<?php

namespace App\Filament\Resources\Provas\Tables;

use App\Models\Prova;
use App\Services\OmrService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable()->sortable(),
                TextColumn::make('etapa.ordem')->label('Etapa')->searchable()->sortable(),
                TextColumn::make('caderno')->label('Caderno')->sortable()->toggleable(),
                TextColumn::make('qtd_questoes')->label('Qtd questoes')->sortable()->toggleable(),
                TextColumn::make('leituras_count')->counts('leituras')->label('Cartões lidos')->badge()->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('gerar_cartoes_omr')
                    ->label('Gerar cartões (OMR)')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading('Gerar cartões-resposta para leitura óptica')
                    ->modalDescription('Um cartão por inscrição confirmada, com marcas de referência e nº de inscrição pré-impresso. Imprima, aplique e depois leia com "Ler cartões".')
                    ->modalSubmitActionLabel('Gerar PDF')
                    ->schema([
                        Select::make('num_opcoes')->label('Alternativas por questão')
                            ->options([4 => 'A–D', 5 => 'A–E'])->default(5)->selectablePlaceholder(false),
                        TextInput::make('id_digits')->label('Dígitos do nº de inscrição')
                            ->numeric()->minValue(3)->maxValue(10)->default(6),
                    ])
                    ->action(function (Prova $record, array $data) {
                        $svc = app(OmrService::class);
                        if (! $svc->disponivel()) {
                            Notification::make()->title('Serviço OMR indisponível')
                                ->body('O leitor de cartões (omr-service) não respondeu. Verifique o container.')
                                ->danger()->send();
                            return null;
                        }
                        $r = $svc->gerarCartoesPdf($record, (int) $data['num_opcoes'], (int) $data['id_digits']);
                        if (! ($r['ok'] ?? false)) {
                            Notification::make()->title('Não foi possível gerar')
                                ->body($r['erro'] ?? 'Erro desconhecido.')->danger()->send();
                            return null;
                        }
                        if (! empty($r['ignorados'])) {
                            Notification::make()->title('Alguns candidatos foram ignorados')
                                ->body(count($r['ignorados']).' inscrição(ões) com número não-numérico ficaram de fora do PDF.')
                                ->warning()->send();
                        }
                        $pdf = $r['pdf'];
                        $nome = 'cartoes-omr-'.str($record->nome ?: 'prova')->slug().'.pdf';
                        return response()->streamDownload(fn () => print($pdf), $nome, ['Content-Type' => 'application/pdf']);
                    }),

                Action::make('ler_cartoes_omr')
                    ->label('Ler cartões (OMR)')
                    ->icon('heroicon-o-camera')
                    ->color('success')
                    ->modalHeading('Ler cartões-resposta digitalizados')
                    ->modalDescription('Envie as imagens/fotos dos cartões desta prova. O sistema lê as marcações, casa pelo nº de inscrição e lança as respostas; a nota é calculada automaticamente pelo gabarito. Reenviar um cartão sobrescreve a leitura anterior.')
                    ->modalSubmitActionLabel('Ler e lançar')
                    ->schema([
                        Select::make('num_opcoes')->label('Alternativas por questão')
                            ->options([4 => 'A–D', 5 => 'A–E'])->default(5)->selectablePlaceholder(false),
                        TextInput::make('id_digits')->label('Dígitos do nº de inscrição')
                            ->numeric()->minValue(3)->maxValue(10)->default(6),
                        FileUpload::make('imagens')->label('Imagens dos cartões')
                            ->image()->multiple()->storeFiles(false)
                            ->maxFiles(300)->maxSize(8192)
                            ->helperText('PNG/JPG. Pode enviar vários de uma vez.'),
                    ])
                    ->action(function (Prova $record, array $data) {
                        $svc = app(OmrService::class);
                        if (! $svc->disponivel()) {
                            Notification::make()->title('Serviço OMR indisponível')
                                ->body('O leitor de cartões (omr-service) não respondeu.')->danger()->send();
                            return;
                        }
                        $files = $data['imagens'] ?? [];
                        if (empty($files)) {
                            Notification::make()->title('Nenhuma imagem enviada')->warning()->send();
                            return;
                        }
                        $imagens = [];
                        foreach ($files as $f) {
                            $imagens[] = [$f->get(), $f->getClientOriginalName()];
                        }
                        $imp = $svc->processarUploads($record, $imagens, (int) $data['num_opcoes'], (int) $data['id_digits']);

                        $avisos = collect($imp->erros_detalhe ?? [])->filter(fn ($e) => ! empty($e['aviso']))->count();
                        $falhas = collect($imp->erros_detalhe ?? [])->filter(fn ($e) => empty($e['aviso']))->count();

                        Notification::make()
                            ->title('Leitura concluída')
                            ->body("Lançados: {$imp->importados} de {$imp->total}. Com alertas p/ revisar: {$avisos}. Não casados/falhas: {$falhas}.")
                            ->color($falhas > 0 ? 'warning' : 'success')
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
