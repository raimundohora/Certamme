<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrecaoSubjetiva extends Model
{
    use BelongsToTenant;

    protected $table = 'correcoes_subjetivas';
    protected $fillable = ['tenant_id', 'prova_subjetiva_id', 'corretor', 'nota', 'quesitos'];
    protected $casts = ['nota' => 'decimal:2', 'quesitos' => 'array'];

    public function prova(): BelongsTo { return $this->belongsTo(ProvaSubjetiva::class, 'prova_subjetiva_id'); }
}
