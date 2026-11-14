<?php
namespace App\Enums;

enum StatusRecurso: string
{
    case PENDENTE = 'pendente';
    case DEFERIDO = 'deferido';
    case INDEFERIDO = 'indeferido';
}
