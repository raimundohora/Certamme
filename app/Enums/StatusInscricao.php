<?php
namespace App\Enums;

enum StatusInscricao: string
{
    case PENDENTE = 'pendente';
    case CONFIRMADA = 'confirmada';
    case CANCELADA = 'cancelada';
}
