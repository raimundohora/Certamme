<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Candidato extends Authenticatable
{
    use BelongsToTenant, Notifiable;

    protected $table = 'candidatos';
    protected $fillable = [
        'tenant_id', 'nome', 'cpf', 'email', 'data_nascimento', 'telefone', 'password',
        'sexo', 'nome_social', 'nome_mae', 'rg', 'cep', 'logradouro', 'numero',
        'bairro', 'cidade', 'uf', 'pcd', 'tipo_deficiencia',
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['data_nascimento' => 'date', 'password' => 'hashed', 'pcd' => 'boolean'];

    public function inscricoes(): HasMany { return $this->hasMany(Inscricao::class); }

    /** Cadastro minimo pra poder se inscrever (padrao ProSeleta). */
    public function perfilCompleto(): bool
    {
        foreach (['data_nascimento', 'sexo', 'nome_mae', 'cep', 'cidade', 'uf'] as $campo) {
            if (blank($this->{$campo})) {
                return false;
            }
        }
        return true;
    }
}
