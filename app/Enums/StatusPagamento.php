<?php
namespace App\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'pendente';
    case PAGO = 'pago';
    case CANCELADO = 'cancelado';
    case ESTORNADO = 'estornado';
}
