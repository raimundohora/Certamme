<?php
namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class SetPortalTenant
{
    public function handle(Request $request, Closure $next)
    {
        // MVP: usa a 1a organizadora. (multi-portal por subdominio vem depois)
        if (! Tenant::current()) {
            Tenant::setCurrent(Tenant::query()->first());
        }
        return $next($request);
    }
}
