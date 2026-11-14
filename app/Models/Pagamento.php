<?php
namespace App\Models;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Pagamento extends Model implements Auditable
{
    use BelongsToTenant, AuditableTrait;

    protected $table = 'pagamentos';
    protected $fillable = [
        'tenant_id', 'inscricao_id', 'valor', 'metodo', 'status',
        'external_id', 'secure_url', 'linha_digitavel', 'pix_qrcode', 'pago_em',
    ];
    protected $casts = [
        'valor'   => 'decimal:2',
        'metodo'  => MetodoPagamento::class,
        'status'  => StatusPagamento::class,
        'pago_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
}
