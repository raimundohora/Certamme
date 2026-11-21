<?php

namespace App\Filament\Widgets;

use App\Models\Inscricao;
use App\Models\Pagamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArrecadacaoOverview extends BaseWidget
{
    protected static ?int $sort = -3;

    protected ?string $heading = 'Financeiro';

    protected function getStats(): array
    {
        $fmt = fn (float $v): string => 'R$ ' . number_format($v, 2, ',', '.');

        $pagoTotal = (float) Pagamento::where('status', 'pago')->sum('valor');
        $pagoQtd = Pagamento::where('status', 'pago')->count();
        $pendTotal = (float) Pagamento::where('status', 'pendente')->sum('valor');
        $pendQtd = Pagamento::where('status', 'pendente')->count();
        $cancTotal = (float) Pagamento::whereIn('status', ['cancelado', 'estornado'])->sum('valor');
        $cancQtd = Pagamento::whereIn('status', ['cancelado', 'estornado'])->count();

        return [
            Stat::make('Arrecadado', $fmt($pagoTotal))
                ->description($pagoQtd . ' pagamento(s) confirmado(s)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('A receber (pendente)', $fmt($pendTotal))
                ->description($pendQtd . ' aguardando pagamento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Cancelado / estornado', $fmt($cancTotal))
                ->description($cancQtd . ' cobrança(s)')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('gray'),
            Stat::make('Inscrições', (string) Inscricao::count())
                ->description('total no sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
