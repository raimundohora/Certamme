<?php
namespace App\Models;

use App\Enums\SituacaoQuestao;
use App\Models\Concerns\BelongsToTenant;
use App\Observers\GabaritoItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy(GabaritoItemObserver::class)]
class GabaritoItem extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

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

    public function respostaEfetiva(): ?string
    {
        return match ($this->situacao) {
            SituacaoQuestao::ANULADA  => null,
            SituacaoQuestao::ALTERADA => $this->resposta_nova,
            default                   => $this->resposta_correta,
        };
    }
}
