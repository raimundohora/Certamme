<?php
namespace App\Models;

use App\Enums\TipoCampo;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CampoInscricao extends Model
{
    use BelongsToTenant;

    protected $table = 'campos_inscricao';
    protected $fillable = [
        'tenant_id', 'concurso_id', 'rotulo', 'chave', 'tipo',
        'opcoes', 'obrigatorio', 'visivel', 'ordem', 'ajuda',
    ];
    protected $casts = [
        'tipo' => TipoCampo::class,
        'opcoes' => 'array',
        'obrigatorio' => 'boolean',
        'visivel' => 'boolean',
        'ordem' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (CampoInscricao $campo) {
            if (blank($campo->chave)) {
                $campo->chave = Str::slug($campo->rotulo, '_') ?: ('campo_' . Str::random(6));
            }
        });
    }

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
}
