<?php
namespace App\Enums;

enum TipoRecurso: string
{
    case GABARITO = 'gabarito';
    case NOTA = 'nota';
    case ISENCAO = 'isencao';
    case OUTROS = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::GABARITO => 'Gabarito',
            self::NOTA     => 'Nota',
            self::ISENCAO  => 'Indeferimento de isenção',
            self::OUTROS   => 'Outros',
        };
    }
}
