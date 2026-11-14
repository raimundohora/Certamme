<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alocacao extends Model
{
    use BelongsToTenant;

    protected $table = 'alocacoes';
    protected $fillable = ['tenant_id', 'etapa_id', 'inscricao_id', 'sala_id', 'carteira'];
    protected $casts = ['carteira' => 'integer'];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function sala(): BelongsTo { return $this->belongsTo(Sala::class); }
    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
