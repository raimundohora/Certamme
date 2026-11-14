<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cronograma extends Model
{
    use BelongsToTenant;

    protected $table = 'cronogramas';
    protected $fillable = ['tenant_id', 'concurso_id', 'evento', 'data'];
    protected $casts = ['data' => 'date'];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
}
