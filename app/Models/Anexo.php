<?php
namespace App\Models;

use App\Enums\GrupoAnexo;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Anexo extends Model
{
    use BelongsToTenant;

    protected $table = 'anexos';
    protected $fillable = ['tenant_id', 'concurso_id', 'titulo', 'grupo', 'arquivo', 'publicado'];
    protected $casts = ['grupo' => GrupoAnexo::class, 'publicado' => 'boolean'];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }

    public function url(): ?string
    {
        return $this->arquivo ? Storage::disk('public')->url($this->arquivo) : null;
    }
}
