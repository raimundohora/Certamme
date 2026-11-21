<?php

namespace App\Http\Controllers;

use App\Services\PagamentoService;
use Illuminate\Http\Request;

/**
 * Recebe o webhook do Asaas e confirma o pagamento (conciliacao).
 * Valida por token (header asaas-access-token) se configurado.
 */
class AsaasWebhookController extends Controller
{
    public function __invoke(Request $request, PagamentoService $pagamentos)
    {
        $token = config('services.asaas.webhook_token');
        if ($token && ! hash_equals((string) $token, (string) $request->header('asaas-access-token'))) {
            abort(401);
        }

        $event = (string) $request->input('event');
        $paymentId = $request->input('payment.id');

        if ($paymentId && in_array($event, ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'], true)) {
            $pagamentos->confirmarPorExternalId((string) $paymentId);
        }

        return response()->json(['ok' => true]);
    }
}
