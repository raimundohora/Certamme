<?php
namespace App\Enums;

enum TipoEtapa: string
{
    case OBJETIVA = 'objetiva';
    case REDACAO = 'redacao';
    case TITULOS = 'titulos';
    case PRATICA = 'pratica';
    case MEDIA = 'media';
    case DOCUMENTAL = 'documental';

    public function label(): string
    {
        return match ($this) {
            self::OBJETIVA   => 'Prova Objetiva',
            self::REDACAO    => 'Redação/Discursiva',
            self::TITULOS    => 'Títulos',
            self::PRATICA    => 'Prova Prática',
            self::MEDIA      => 'Média/Final',
            self::DOCUMENTAL => 'Documental/Investigação',
        };
    }
}
