<?php
namespace App\Enums;

enum OrigemLeitura: string
{
    case LEITORA = 'leitora';
    case CSV = 'csv';
    case OMR = 'omr';
}
