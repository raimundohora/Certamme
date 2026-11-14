<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Convocacao extends Model
{
    use BelongsToTenant;

    protected $table = 'convocacoes';
    protected $fillable = ['tenant_id', 'etapa_id', 'inscricao_id', 'publicado', 'observacao'];
    protected $casts = ['publicado' => 'boolean'];

    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
}
