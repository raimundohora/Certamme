<?php
namespace App\Services;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use App\Mail\PagamentoConfirmado;
use App\Models\Inscricao;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PagamentoService
{
    public function criar(Inscricao $inscricao, float $valor, MetodoPagamento $metodo): Pagamento
    {
        return Pagamento::updateOrCreate(
            ['inscricao_id' => $inscricao->id],
            ['valor' => $valor, 'metodo' => $metodo, 'status' => StatusPagamento::PENDENTE],
        );
    }

    public function confirmarPorExternalId(string $externalId): ?Pagamento
    {
        $pag = Pagamento::where('external_id', $externalId)->first();
        if (! $pag) {
            return null;
        }
        if ($pag->status !== StatusPagamento::PAGO) {
            $pag->update(['status' => StatusPagamento::PAGO, 'pago_em' => now()]);

            // e-mail de confirmacao
            $pag->loadMissing('inscricao.candidato', 'inscricao.concurso');
            $email = optional($pag->inscricao->candidato)->email;
            if ($email) {
                try {
                    Mail::to($email)->send(new PagamentoConfirmado($pag));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return $pag->refresh();
    }

    /**
     * Cria a cobranca no gateway (Asaas) e grava os dados no pagamento.
     * Idempotente: se ja tem external_id, nao faz nada.
     */
    public function criarNoGateway(Pagamento $pagamento): void
    {
        if ($pagamento->external_id) {
            return;
        }

        $pagamento->loadMissing('inscricao.candidato');
        $candidato = $pagamento->inscricao?->candidato;
        if (! $candidato) {
            throw new RuntimeException('Pagamento sem candidato para gerar cobranca.');
        }

        $gw = app(AsaasGateway::class);
        $customerId = $gw->cliente($candidato);
        $cob = $gw->cobranca($customerId, $pagamento);

        $dados = [
            'external_id' => $cob['id'] ?? null,
            'secure_url' => $cob['invoiceUrl'] ?? null,
            'linha_digitavel' => $cob['identificationField'] ?? null,
        ];

        if ($pagamento->metodo === MetodoPagamento::PIX && ! empty($cob['id'])) {
            $qr = $gw->pixQrCode((string) $cob['id']);
            $dados['pix_qrcode'] = $qr['payload'] ?? null;
        }

        $pagamento->update($dados);
    }
}
