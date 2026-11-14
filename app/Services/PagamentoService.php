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

    public function criarNoGateway(Pagamento $pagamento): void
    {
        throw new RuntimeException('Integracao Iugu pendente: configurar IUGU_API_TOKEN e implementar POST /invoices.');
    }
}
