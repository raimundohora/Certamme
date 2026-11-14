<?php
namespace App\Models;

use App\Enums\StatusDocumento;
use App\Enums\TipoDocumento;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use BelongsToTenant;

    protected $table = 'documentos';
    protected $fillable = ['tenant_id', 'inscricao_id', 'tipo', 'arquivo', 'status', 'observacao'];
    protected $casts = ['tipo' => TipoDocumento::class, 'status' => StatusDocumento::class];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
    public function url(): ?string { return $this->arquivo ? Storage::disk('public')->url($this->arquivo) : null; }
}
