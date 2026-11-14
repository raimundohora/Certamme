<?php
namespace App\Enums;

enum StatusIsencao: string
{
    case PENDENTE = 'pendente';
    case DEFERIDO = 'deferido';
    case INDEFERIDO = 'indeferido';
}
