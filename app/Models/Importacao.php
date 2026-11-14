<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Importacao extends Model
{
    use BelongsToTenant;

    protected $table = 'importacoes';
    protected $fillable = ['tenant_id', 'prova_id', 'arquivo', 'total', 'importados', 'erros', 'erros_detalhe'];
    protected $casts = [
        'total' => 'integer', 'importados' => 'integer', 'erros' => 'integer',
        'erros_detalhe' => 'array',
    ];

    public function prova(): BelongsTo { return $this->belongsTo(Prova::class); }
}
