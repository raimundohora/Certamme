<?php
namespace App\Enums;

enum Modalidade: string
{
    case AMPLA = 'ampla';
    case COTA_PCD = 'cota_pcd';
    case COTA_RACIAL = 'cota_racial';
    case COTA_INDIGENA = 'cota_indigena';

    public function label(): string
    {
        return match ($this) {
            self::AMPLA         => 'Ampla concorrência',
            self::COTA_PCD      => 'Cota PcD',
            self::COTA_RACIAL   => 'Cota racial',
            self::COTA_INDIGENA => 'Cota indígena',
        };
    }
}
