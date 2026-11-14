<?php
namespace App\Enums;

enum StatusSubjetiva: string
{
    case PENDENTE = 'pendente';
    case CORRIGIDA = 'corrigida';
    case DIVERGENTE = 'divergente';
}
