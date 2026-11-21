<?php

namespace App\Filament\Widgets;

use App\Models\Inscricao;
use App\Support\ConcursoAtual;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InscricoesOverview extends BaseWidget
{
    protected static ?int $sort = -4;

    protected ?string $heading = 'Visão Geral do concurso';

    public static function canView(): bool
    {
        return ConcursoAtual::id() !== null;
    }

    protected function getStats(): array
    {
        $cid = ConcursoAtual::id();
        $base = Inscricao::where('concurso_id', $cid);
        return [
            Stat::make('Inscrições', (string) (clone $base)->count())
                ->descriptionIcon('heroicon-m-user-group')->color('primary'),
            Stat::make('Confirmadas', (string) (clone $base)->where('status', 'confirmada')->count())
                ->descriptionIcon('heroicon-m-check-circle')->color('success'),
            Stat::make('Pendentes', (string) (clone $base)->where('status', 'pendente')->count())
                ->descriptionIcon('heroicon-m-clock')->color('warning'),
            Stat::make('Canceladas', (string) (clone $base)->where('status', 'cancelada')->count())
                ->descriptionIcon('heroicon-m-x-circle')->color('gray'),
        ];
    }
}
