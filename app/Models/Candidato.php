<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    use BelongsToTenant;

    protected $table = 'candidatos';
    protected $fillable = ['tenant_id', 'nome', 'cpf', 'email', 'data_nascimento', 'telefone'];
    protected $casts = ['data_nascimento' => 'date'];

    public function inscricoes(): HasMany { return $this->hasMany(Inscricao::class); }
}
