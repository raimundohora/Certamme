<?php
namespace App\Enums;

enum SituacaoQuestao: string
{
    case VALIDA = 'valida';
    case ANULADA = 'anulada';
    case ALTERADA = 'alterada';

    public function label(): string
    {
        return match ($this) {
            self::VALIDA   => 'Válida',
            self::ANULADA  => 'Anulada (ponto para todos)',
            self::ALTERADA => 'Gabarito alterado',
        };
    }
}
