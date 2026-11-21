<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cargo extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'concurso_id', 'nome', 'vagas', 'vagas_cotas', 'taxa_inscricao'];
    protected $casts = [
        'vagas'        => 'integer',
        'vagas_cotas'  => 'array',
        'taxa_inscricao' => 'decimal:2',
    ];

    public function concurso(): BelongsTo { return $this->belongsTo(Concurso::class); }

    /**
     * Vagas para uma modalidade. Ampla = total - reservadas; cota = o que estiver configurado (0 se nada).
     */
    public function vagasPara(string $modalidade): int
    {
        $cotas = $this->vagas_cotas ?? [];
        if ($modalidade === 'ampla') {
            $reservadas = array_sum(array_map('intval', $cotas));
            return max(0, (int) $this->vagas - $reservadas);
        }
        return array_key_exists($modalidade, $cotas) ? (int) $cotas[$modalidade] : 0;
    }
}
