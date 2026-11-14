<?php
namespace App\Enums;

enum StatusDocumento: string
{
    case PENDENTE = 'pendente';
    case APROVADO = 'aprovado';
    case REPROVADO = 'reprovado';
}
