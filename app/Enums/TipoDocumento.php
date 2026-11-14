<?php
namespace App\Enums;

enum TipoDocumento: string
{
    case LAUDO_PCD = 'laudo_pcd';
    case TITULO = 'titulo';
    case ISENCAO = 'isencao';
    case OUTROS = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::LAUDO_PCD => 'Laudo (PcD)',
            self::TITULO    => 'Título',
            self::ISENCAO   => 'Documento de isenção',
            self::OUTROS    => 'Outros',
        };
    }
}
