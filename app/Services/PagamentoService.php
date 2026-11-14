<?php
namespace App\Services;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use App\Models\Inscricao;
use App\Models\Pagamento;
use RuntimeException;

class PagamentoService
{
    /** Cria o registro local da cobranca (pendente). 1 por inscricao. */
    public function criar(Inscricao $inscricao, float $valor, MetodoPagamento $metodo): Pagamento
    {
        return Pagamento::updateOrCreate(
            ['inscricao_id' => $inscricao->id],
            ['valor' => $valor, 'metodo' => $metodo, 'status' => StatusPagamento::PENDENTE],
        );
    }

    /** Conciliacao por webhook/retorno: acha pela fatura do gateway e marca pago (idempotente). */
    public function confirmarPorExternalId(string $externalId): ?Pagamento
    {
        $pag = Pagamento::where('external_id', $externalId)->first();
        if (! $pag) {
            return null;
        }
        if ($pag->status !== StatusPagamento::PAGO) {
            $pag->update(['status' => StatusPagamento::PAGO, 'pago_em' => now()]);
        }

        return $pag->refresh();
    }

    /**
     * Cria a cobranca no gateway (Iugu) -> grava external_id/secure_url/pix.
     * PENDENTE: precisa de IUGU_API_TOKEN. Contrato em docs/sigecon/integracoes-contratos.md
     * (POST https://api.iugu.com/v1/invoices, payable_with = pix|bank_slip, external_reference = inscricao).
     */
    public function criarNoGateway(Pagamento $pagamento): void
    {
        throw new RuntimeException('Integracao Iugu pendente: configurar IUGU_API_TOKEN e implementar POST /invoices.');
    }
}
