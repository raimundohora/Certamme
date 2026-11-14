<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concurso extends Model
{
    use BelongsToTenant;

    protected $table = 'concursos';
    protected $fillable = ['tenant_id', 'nome', 'ano', 'situacao'];

    public function cargos(): HasMany { return $this->hasMany(Cargo::class); }
}
