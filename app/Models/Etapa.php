<?php
namespace App\Models;

use App\Enums\TipoEtapa;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etapa extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'concurso_id', 'cargo_id', 'ordem', 'tipo',
        'eliminatoria', 'classificatoria', 'nota_minima', 'peso', 'config',
    ];

    protected $casts = [
        'tipo'            => TipoEtapa::class,
        'eliminatoria'    => 'boolean',
        'classificatoria' => 'boolean',
        'nota_minima'     => 'decimal:2',
        'peso'            => 'decimal:2',
        'config'          => 'array',
    ];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }
}
