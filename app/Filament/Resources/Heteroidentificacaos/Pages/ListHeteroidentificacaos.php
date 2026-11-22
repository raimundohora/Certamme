<?php

namespace App\Filament\Resources\Heteroidentificacaos\Pages;

use App\Enums\Modalidade;
use App\Enums\StatusHeteroidentificacao;
use App\Filament\Resources\Heteroidentificacaos\HeteroidentificacaoResource;
use App\Models\Heteroidentificacao;
use App\Models\Inscricao;
use App\Support\ConcursoAtual;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListHeteroidentificacaos extends ListRecords
{
    protected static string $resource = HeteroidentificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('gerar_pendencias')
                ->label('Gerar pendências de cota')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Cria uma verificação pendente para cada inscrição de cota racial/indígena deste concurso que ainda não tem uma. Não altera as já existentes.')
                ->action(function () {
                    $cid = ConcursoAtual::id();
                    if (! $cid) {
                        Notification::make()->title('Selecione um concurso')->warning()->send();
                        return;
                    }

                    $inscricoes = Inscricao::where('concurso_id', $cid)
                        ->whereIn('modalidade', [Modalidade::COTA_RACIAL->value, Modalidade::COTA_INDIGENA->value])
                        ->whereDoesntHave('heteroidentificacao')
                        ->get(['id', 'tenant_id']);

                    $criadas = 0;
                    foreach ($inscricoes as $insc) {
                        Heteroidentificacao::create([
                            'tenant_id'    => $insc->tenant_id,
                            'inscricao_id' => $insc->id,
                            'status'       => StatusHeteroidentificacao::PENDENTE->value,
                        ]);
                        $criadas++;
                    }

                    Notification::make()
                        ->title($criadas > 0 ? "{$criadas} pendência(s) criada(s)" : 'Nada a criar')
                        ->body($criadas > 0 ? 'Avalie cada uma e registre o parecer da comissão.' : 'Todas as inscrições de cota racial/indígena já têm verificação.')
                        ->color($criadas > 0 ? 'success' : 'gray')
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
