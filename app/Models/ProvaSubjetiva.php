<?php
namespace App\Models;

use App\Enums\StatusSubjetiva;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ProvaSubjetiva extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'provas_subjetivas';
    protected $fillable = ['tenant_id', 'etapa_id', 'inscricao_id', 'codigo_controle', 'nota_final', 'status'];
    protected $casts = [
        'nota_final' => 'decimal:2',
        'status'     => StatusSubjetiva::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (ProvaSubjetiva $ps) {
            if (blank($ps->codigo_controle)) {
                $ps->codigo_controle = strtoupper(Str::random(8));
            }
        });
    }

    public function etapa(): BelongsTo { return $this->belongsTo(Etapa::class); }
    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function correcoes(): HasMany { return $this->hasMany(CorrecaoSubjetiva::class); }
}
