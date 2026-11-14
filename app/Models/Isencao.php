<?php
namespace App\Models;

use App\Enums\StatusIsencao;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Isencao extends Model
{
    use BelongsToTenant;

    protected $table = 'isencoes';
    protected $fillable = ['tenant_id', 'inscricao_id', 'nis', 'justificativa', 'status', 'motivo'];
    protected $casts = ['status' => StatusIsencao::class];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
}
