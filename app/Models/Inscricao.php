<?php
namespace App\Models;

use App\Enums\Modalidade;
use App\Enums\StatusInscricao;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    use BelongsToTenant;

    protected $table = 'inscricoes';
    protected $fillable = [
        'tenant_id', 'concurso_id', 'candidato_id', 'cargo_id',
        'numero_inscricao', 'nome', 'modalidade', 'status',
    ];
    protected $casts = [
        'modalidade' => Modalidade::class,
        'status'     => StatusInscricao::class,
    ];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
    public function candidato(): BelongsTo { return $this->belongsTo(Candidato::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }
}
