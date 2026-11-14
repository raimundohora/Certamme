<?php
namespace App\Models;

use App\Enums\SituacaoQuestao;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class GabaritoItem extends Model
{
    use BelongsToTenant;

    protected $table = 'gabarito_itens';
    protected $fillable = [
        'tenant_id', 'prova_id', 'numero', 'disciplina',
        'resposta_correta', 'situacao', 'resposta_nova', 'peso',
    ];
    protected $casts = [
        'situacao' => SituacaoQuestao::class,
        'numero'   => 'integer',
        'peso'     => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (GabaritoItem $item) {
            if ($item->situacao === SituacaoQuestao::ALTERADA && blank($item->resposta_nova)) {
                throw new InvalidArgumentException('Questão ALTERADA exige resposta_nova.');
            }
        });
    }

    public function prova(): BelongsTo { return $this->belongsTo(Prova::class); }

    /** Resposta que vale no cálculo, considerando a situação da questão. */
    public function respostaEfetiva(): ?string
    {
        return match ($this->situacao) {
            SituacaoQuestao::ANULADA  => null,                 // anulada = ponto p/ todos (tratado no cálculo)
            SituacaoQuestao::ALTERADA => $this->resposta_nova, // alterada usa a nova
            default                   => $this->resposta_correta,
        };
    }
}
