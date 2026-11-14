<?php
namespace App\Enums;

enum IdentificacaoOmr: string
{
    case CODIGO_BARRAS = 'codigo_barras';
    case QR = 'qr';
    case MARCACAO = 'marcacao';
    case NOME_ARQUIVO = 'nome_arquivo';
}
