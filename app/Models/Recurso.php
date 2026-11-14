<?php
namespace App\Models;

use App\Enums\StatusRecurso;
use App\Enums\TipoRecurso;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Recurso extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'recursos';
    protected $fillable = ['tenant_id', 'inscricao_id', 'etapa_id', 'tipo', 'questao', 'texto', 'status', 'resposta'];
    protected $casts = ['tipo' => TipoRecurso::class, 'status' => StatusRecurso::class, 'questao' => 'integer'];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
}
