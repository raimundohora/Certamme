<?php
namespace App\Enums;

enum MetodoPagamento: string
{
    case PIX = 'pix';
    case BOLETO = 'boleto';
    case CARTAO = 'cartao';
}
