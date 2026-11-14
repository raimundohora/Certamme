<?php
namespace App\Enums;

enum StatusTitulo: string
{
    case PENDENTE = 'pendente';
    case AVALIADO = 'avaliado';
    case CONFERIDO = 'conferido';
}
