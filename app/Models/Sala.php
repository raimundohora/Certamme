<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sala extends Model
{
    use BelongsToTenant;

    protected $table = 'salas';
    protected $fillable = ['tenant_id', 'local_prova_id', 'nome', 'capacidade'];
    protected $casts = ['capacidade' => 'integer'];

    public function local(): BelongsTo { return $this->belongsTo(LocalProva::class, 'local_prova_id'); }
}
