<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prova extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'etapa_id', 'nome', 'caderno', 'qtd_questoes'];
    protected $casts = ['qtd_questoes' => 'integer'];

    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
    public function itens(): HasMany { return $this->hasMany(GabaritoItem::class); }
}
