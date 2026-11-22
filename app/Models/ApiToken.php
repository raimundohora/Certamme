<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Token de API de uma organizadora. NÃO usa BelongsToTenant: a busca pelo hash
 * acontece ANTES de o tenant ser resolvido (é justamente o token que o define).
 */
class ApiToken extends Model
{
    protected $table = 'api_tokens';

    protected $fillable = ['tenant_id', 'nome', 'prefixo', 'token_hash', 'ultimo_uso_at'];

    protected $casts = ['ultimo_uso_at' => 'datetime'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
