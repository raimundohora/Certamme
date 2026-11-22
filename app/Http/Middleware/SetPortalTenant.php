<?php
namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

/**
 * Resolve a organizadora (tenant) pelo host da requisição, ativando o escopo
 * global de dados (BelongsToTenant) para o resto do ciclo. Ordem:
 *   1. domínio próprio do cliente  (tenants.dominio == host);
 *   2. subdomínio do domínio-base  ({slug}.base_domain  -> tenants.slug);
 *   3. host legado / domínio-base  -> organizadora padrão (config) ou a 1ª ativa.
 * Subdomínio informado mas inexistente -> 404 (não cai no fallback).
 * Marca `certamme_apex` quando o host é o próprio domínio-base (p/ a vitrine).
 */
class SetPortalTenant
{
    /** Subdomínios reservados que nunca são slug de organizadora. */
    private const RESERVADOS = ['www', 'admin', 'app', 'api'];

    public function handle(Request $request, Closure $next)
    {
        $host = strtolower($request->getHost());
        $base = strtolower((string) config('certamme.base_domain'));

        if ($host === $base || $host === 'www.' . $base) {
            $request->attributes->set('certamme_apex', true);
        }

        if (! Tenant::current()) {
            if ($tenant = $this->resolver($request, $host, $base)) {
                Tenant::setCurrent($tenant);
            }
        }

        return $next($request);
    }

    private function resolver(Request $request, string $host, string $base): ?Tenant
    {
        // 1. domínio próprio
        $tenant = Tenant::query()->where('dominio', $host)->where('ativo', true)->first();
        if ($tenant) {
            return $tenant;
        }

        // 2. subdomínio do domínio-base
        if ($base !== '' && str_ends_with($host, '.' . $base)) {
            $prefixo = substr($host, 0, -(strlen($base) + 1));
            $slug    = explode('.', $prefixo)[0] ?? '';

            if ($slug !== '' && ! in_array($slug, self::RESERVADOS, true)) {
                $tenant = Tenant::query()->where('slug', $slug)->where('ativo', true)->first();
                abort_if($tenant === null, 404, 'Organizadora não encontrada.');
                return $tenant;
            }
        }

        // 3. fallback (host legado ou o próprio domínio-base)
        return $this->fallback();
    }

    private function fallback(): ?Tenant
    {
        $slug = config('certamme.default_tenant');
        if ($slug) {
            $tenant = Tenant::query()->where('slug', $slug)->where('ativo', true)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        return Tenant::query()->where('ativo', true)->first() ?? Tenant::query()->first();
    }
}
