<?php
namespace App\Enums;

enum StatusHeteroidentificacao: string
{
    case PENDENTE = 'pendente';
    case DEFERIDA = 'deferida';
    case INDEFERIDA = 'indeferida';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE   => 'Pendente',
            self::DEFERIDA   => 'Deferida (cota confirmada)',
            self::INDEFERIDA => 'Indeferida (concorre na ampla)',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::PENDENTE   => 'warning',
            self::DEFERIDA   => 'success',
            self::INDEFERIDA => 'danger',
        };
    }
}
