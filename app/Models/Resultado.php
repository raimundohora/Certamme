<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Resultado extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'resultados';
    protected $fillable = [
        'tenant_id', 'etapa_id', 'inscricao_id',
        'total', 'acertos', 'eliminado', 'classificacao', 'calculado_em',
    ];
    protected $casts = [
        'total'         => 'decimal:2',
        'acertos'       => 'integer',
        'eliminado'     => 'boolean',
        'classificacao' => 'integer',
        'calculado_em'  => 'datetime',
    ];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
