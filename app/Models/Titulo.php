<?php
namespace App\Models;

use App\Enums\StatusTitulo;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Titulo extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'titulos';
    protected $fillable = ['tenant_id', 'inscricao_id', 'titulo_tipo_id', 'pontos', 'status', 'avaliador', 'conferente'];
    protected $casts = [
        'pontos' => 'decimal:2',
        'status' => StatusTitulo::class,
    ];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function tipo(): BelongsTo { return $this->belongsTo(TituloTipo::class, 'titulo_tipo_id'); }
}
