<?php
namespace App\Models;

use App\Enums\StatusHeteroidentificacao;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Verificação de heteroidentificação (comissão de cota racial/indígena). Uma por inscrição.
 * Indeferida => o candidato passa a concorrer apenas na ampla (ver Inscricao::modalidadeEfetiva).
 */
class Heteroidentificacao extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'heteroidentificacoes';

    protected $fillable = [
        'tenant_id', 'inscricao_id', 'status', 'parecer', 'avaliador', 'data_avaliacao',
    ];

    protected $casts = [
        'status'         => StatusHeteroidentificacao::class,
        'data_avaliacao' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Heteroidentificacao $het) {
            $decidida = in_array($het->status, [
                StatusHeteroidentificacao::DEFERIDA,
                StatusHeteroidentificacao::INDEFERIDA,
            ], true);

            if ($decidida && $het->data_avaliacao === null) {
                $het->data_avaliacao = now();
            }
            if ($decidida && blank($het->avaliador)) {
                $het->avaliador = auth()->user()?->name ?? 'Comissão';
            }
        });
    }

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }
}
