<?php
namespace App\Models;

use App\Enums\OrigemLeitura;
use App\Models\Concerns\BelongsToTenant;
use App\Observers\LeituraCartaoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(LeituraCartaoObserver::class)]
class LeituraCartao extends Model
{
    use BelongsToTenant;

    protected $table = 'leituras_cartao';
    protected $fillable = ['tenant_id', 'prova_id', 'inscricao_id', 'importacao_id', 'respostas', 'origem'];
    protected $casts = [
        'respostas' => 'array',
        'origem'    => OrigemLeitura::class,
    ];

    public function prova(): BelongsTo { return $this->belongsTo(Prova::class); }
    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
}
