<?php
namespace App\Enums;

enum GrupoAnexo: string
{
    case EDITAL = 'edital';
    case COMUNICADO = 'comunicado';
    case RESULTADO = 'resultado';
    case GABARITO = 'gabarito';
    case OUTROS = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::EDITAL     => 'Edital',
            self::COMUNICADO => 'Comunicado',
            self::RESULTADO  => 'Resultado',
            self::GABARITO   => 'Gabarito',
            self::OUTROS     => 'Outros',
        };
    }
}
