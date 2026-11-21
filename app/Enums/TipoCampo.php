<?php
namespace App\Enums;

enum TipoCampo: string
{
    case TEXTO = 'texto';
    case TEXTAREA = 'textarea';
    case NUMERO = 'numero';
    case DATA = 'data';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';

    public function label(): string
    {
        return match ($this) {
            self::TEXTO    => 'Texto curto',
            self::TEXTAREA => 'Texto longo',
            self::NUMERO   => 'Número',
            self::DATA     => 'Data',
            self::SELECT   => 'Lista (seleção)',
            self::CHECKBOX => 'Sim/Não (caixa)',
        };
    }
}
