<?php

namespace App\Services;

use App\Models\Candidato;
use App\Models\Pagamento;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Cliente da API do Asaas (gateway de pagamento). Agnostico: o PagamentoService
 * usa estes metodos; trocar de gateway = trocar esta classe.
 */
class AsaasGateway
{
    private function http(): PendingRequest
    {
        return Http::withHeaders([
            'access_token' => (string) config('services.asaas.key'),
            'User-Agent' => 'Certamme/1.0',
        ])->baseUrl(rtrim((string) config('services.asaas.base_url'), '/'))
            ->acceptJson()
            ->timeout(30);
    }

    /** Acha (por externalReference) ou cria o cliente Asaas do candidato. Idempotente. */
    public function cliente(Candidato $c): string
    {
        $ref = 'candidato-' . $c->id;

        $busca = $this->http()->get('/customers', ['externalReference' => $ref]);
        if ($busca->ok() && (int) ($busca->json('totalCount') ?? 0) > 0) {
            return (string) $busca->json('data.0.id');
        }

        $resp = $this->http()->post('/customers', [
            'name' => $c->nome ?: ('Candidato ' . $c->id),
            'cpfCnpj' => preg_replace('/\D/', '', (string) $c->cpf),
            'email' => $c->email ?: null,
            'mobilePhone' => preg_replace('/\D/', '', (string) $c->telefone) ?: null,
            'externalReference' => $ref,
        ]);
        if (! $resp->ok()) {
            throw new RuntimeException('Asaas /customers ' . $resp->status() . ': ' . $resp->body());
        }

        return (string) $resp->json('id');
    }

    /** Cria a cobranca (payment) para o pagamento e devolve o JSON da cobranca. */
    public function cobranca(string $customerId, Pagamento $pag): array
    {
        $billing = match ($pag->metodo?->value) {
            'boleto' => 'BOLETO',
            'cartao' => 'CREDIT_CARD',
            default => 'PIX',
        };

        $resp = $this->http()->post('/payments', [
            'customer' => $customerId,
            'billingType' => $billing,
            'value' => (float) $pag->valor,
            'dueDate' => now()->addDays(3)->toDateString(),
            'externalReference' => (string) $pag->id,
            'description' => 'Taxa de inscricao - pagamento #' . $pag->id,
        ]);
        if (! $resp->ok()) {
            throw new RuntimeException('Asaas /payments ' . $resp->status() . ': ' . $resp->body());
        }

        return $resp->json();
    }

    /** QR Code / copia-e-cola do Pix de uma cobranca. */
    public function pixQrCode(string $paymentId): array
    {
        $resp = $this->http()->get("/payments/{$paymentId}/pixQrCode");

        return $resp->ok() ? $resp->json() : [];
    }
}
