<?php

namespace App\Models;

use App\Enums\TipoCertificado;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Documento oficial emitido a partir de uma inscrição. O `codigo` é público e
 * identifica o documento na página de autenticação; `hash` é a impressão digital
 * (sha256) do snapshot `dados` no momento da emissão.
 */
class Certificado extends Model
{
    use BelongsToTenant;

    protected $table = 'certificados';

    protected $fillable = [
        'tenant_id', 'inscricao_id', 'tipo', 'codigo', 'hash', 'dados', 'emitido_em',
    ];

    protected $casts = [
        'dados'      => 'array',
        'emitido_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }

    public function tipoEnum(): TipoCertificado
    {
        return TipoCertificado::from($this->tipo);
    }
}
