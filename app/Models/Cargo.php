<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cargo extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'concurso_id', 'nome', 'vagas'];
    protected $casts = ['vagas' => 'integer'];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
}
