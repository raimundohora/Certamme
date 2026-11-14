<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TituloTipo extends Model
{
    use BelongsToTenant;

    protected $table = 'titulos_tipos';
    protected $fillable = ['tenant_id', 'etapa_id', 'nome', 'pontuacao_maxima'];
    protected $casts = ['pontuacao_maxima' => 'decimal:2'];

    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
