<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

/**
 * Autentica a requisição de API pelo token bearer e define a organizadora (tenant)
 * dona do token, ativando o escopo global de dados para o resto do ciclo.
 */
class AuthenticateApiTenant
{
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();
        if (! $bearer) {
            return response()->json(['message' => 'Token de API ausente. Envie o cabeçalho Authorization: Bearer <token>.'], 401);
        }

        $token = ApiToken::query()->where('token_hash', hash('sha256', $bearer))->first();
        if (! $token) {
            return response()->json(['message' => 'Token de API inválido.'], 401);
        }

        $tenant = Tenant::query()->find($token->tenant_id);
        if (! $tenant || ! $tenant->ativo) {
            return response()->json(['message' => 'Organizadora inativa ou inexistente.'], 403);
        }

        Tenant::setCurrent($tenant);
        $token->forceFill(['ultimo_uso_at' => now()])->saveQuietly();

        return $next($request);
    }
}
