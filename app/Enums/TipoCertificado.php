<?php

namespace App\Enums;

enum TipoCertificado: string
{
    case COMPROVANTE_INSCRICAO = 'comprovante_inscricao';
    case DECLARACAO_PARTICIPACAO = 'declaracao_participacao';
    case CERTIFICADO_CLASSIFICACAO = 'certificado_classificacao';

    public function label(): string
    {
        return match ($this) {
            self::COMPROVANTE_INSCRICAO   => 'Comprovante de inscrição',
            self::DECLARACAO_PARTICIPACAO => 'Declaração de participação',
            self::CERTIFICADO_CLASSIFICACAO => 'Certificado de classificação',
        };
    }

    /** Título em caixa alta usado no corpo do documento. */
    public function titulo(): string
    {
        return mb_strtoupper($this->label());
    }

    /** Nome do arquivo PDF sugerido. */
    public function arquivo(?string $sufixo = null): string
    {
        $base = str_replace('_', '-', $this->value);
        return $base . ($sufixo ? '-' . $sufixo : '') . '.pdf';
    }
}
