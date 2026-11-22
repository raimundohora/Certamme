<?php
namespace App\Models;

use App\Enums\Modalidade;
use App\Enums\StatusHeteroidentificacao;
use App\Enums\StatusInscricao;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inscricao extends Model
{
    use BelongsToTenant;

    protected $table = 'inscricoes';
    protected $fillable = [
        'tenant_id', 'concurso_id', 'candidato_id', 'cargo_id',
        'numero_inscricao', 'nome', 'modalidade', 'status', 'campos_extras',
    ];
    protected $casts = [
        'modalidade' => Modalidade::class,
        'status'     => StatusInscricao::class,
        'campos_extras' => 'array',
    ];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }
    public function candidato(): BelongsTo { return $this->belongsTo(Candidato::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }
    public function documentos(): HasMany { return $this->hasMany(Documento::class); }
    public function isencao(): HasOne { return $this->hasOne(Isencao::class); }
    public function recursos(): HasMany { return $this->hasMany(Recurso::class); }
    public function convocacoes(): HasMany { return $this->hasMany(Convocacao::class); }
    public function resultados(): HasMany { return $this->hasMany(Resultado::class); }
    public function alocacoes(): HasMany { return $this->hasMany(Alocacao::class); }
    public function heteroidentificacao(): HasOne { return $this->hasOne(Heteroidentificacao::class); }

    /** Cotas que passam por banca de heteroidentificação (racial/indígena). PcD é por laudo. */
    public function exigeHeteroidentificacao(): bool
    {
        return in_array($this->modalidade, [Modalidade::COTA_RACIAL, Modalidade::COTA_INDIGENA], true);
    }

    /** Verdadeiro quando houve decisão indeferindo a cota na heteroidentificação. */
    public function cotaIndeferida(): bool
    {
        return $this->heteroidentificacao?->status === StatusHeteroidentificacao::INDEFERIDA;
    }

    /**
     * Modalidade que vale para a apuração/classificação: se a cota foi indeferida na
     * heteroidentificação, o candidato concorre na ampla; senão, a modalidade declarada.
     */
    public function modalidadeEfetiva(): string
    {
        if ($this->cotaIndeferida()) {
            return Modalidade::AMPLA->value;
        }
        return $this->modalidade?->value ?? Modalidade::AMPLA->value;
    }
}
