<?php
namespace App\Models;

use App\Enums\FormatoMarcacao;
use App\Enums\IdentificacaoOmr;
use App\Enums\OpcoesOmr;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmrCalibracao extends Model
{
    use BelongsToTenant;

    protected $table = 'omr_calibracoes';
    protected $fillable = [
        'tenant_id', 'prova_id', 'identificacao', 'id_chars', 'formato_marcacao',
        'threshold', 'qtde_guias', 'opcoes', 'ignorar_duplicados', 'usar_codigo_prova',
    ];
    protected $casts = [
        'identificacao'      => IdentificacaoOmr::class,
        'formato_marcacao'   => FormatoMarcacao::class,
        'opcoes'             => OpcoesOmr::class,
        'id_chars'           => 'integer',
        'threshold'          => 'integer',
        'qtde_guias'         => 'integer',
        'ignorar_duplicados' => 'boolean',
        'usar_codigo_prova'  => 'boolean',
    ];

    public function prova(): BelongsTo { return $this->belongsTo(Prova::class); }
}
