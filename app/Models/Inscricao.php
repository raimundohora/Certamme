<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    use BelongsToTenant;

    protected $table = 'inscricoes';
    protected $fillable = ['tenant_id', 'concurso_id', 'cargo_id', 'numero_inscricao', 'nome'];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }
}
