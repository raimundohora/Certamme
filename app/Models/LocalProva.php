<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalProva extends Model
{
    use BelongsToTenant;

    protected $table = 'locais_prova';
    protected $fillable = ['tenant_id', 'concurso_id', 'nome', 'endereco', 'cidade'];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
    public function salas(): HasMany { return $this->hasMany(Sala::class); }
}
