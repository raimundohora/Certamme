<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'inscricao_id', 'etapa_id', 'disciplina',
        'acertos', 'nota_bruta', 'nota_final', 'eliminado', 'calculada_em',
    ];
    protected $casts = [
        'acertos'      => 'integer',
        'nota_bruta'   => 'decimal:2',
        'nota_final'   => 'decimal:2',
        'eliminado'    => 'boolean',
        'calculada_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
