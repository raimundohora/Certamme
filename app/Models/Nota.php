<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Nota extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $fillable = [
        'tenant_id', 'inscricao_id', 'etapa_id', 'disciplina',
        'acertos', 'nota_bruta', 'nota_final', 'eliminado', 'calculada_em',
        'override', 'nota_override', 'override_motivo', 'override_por',
    ];
    protected $casts = [
        'acertos'       => 'integer',
        'nota_bruta'    => 'decimal:2',
        'nota_final'    => 'decimal:2',
        'eliminado'     => 'boolean',
        'calculada_em'  => 'datetime',
        'override'      => 'boolean',
        'nota_override' => 'decimal:2',
    ];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
