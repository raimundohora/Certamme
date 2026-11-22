<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    protected $fillable = ['nome', 'slug', 'dominio', 'cor_primaria', 'logo_path', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    protected static ?Tenant $current = null;
    public static function current(): ?Tenant { return static::$current; }
    public static function setCurrent(?Tenant $tenant): void { static::$current = $tenant; }

    /** Subdomínios reservados que nunca são slug de organizadora. */
    private const RESERVADOS = ['www', 'admin', 'app', 'api'];

    /**
     * Resolve a organizadora a partir do host (domínio próprio -> subdomínio -> fallback).
     * Diferente do SetPortalTenant, NUNCA aborta: devolve null quando não há correspondência,
     * para poder ser usado em verificações (ex.: canAccessPanel) sem depender da ordem de middleware.
     */
    public static function resolveByHost(?string $host): ?Tenant
    {
        $host = strtolower((string) $host);
        if ($host === '') {
            return null;
        }
        $base = strtolower((string) config('certamme.base_domain'));

        // 1. domínio próprio
        if ($t = static::query()->where('dominio', $host)->where('ativo', true)->first()) {
            return $t;
        }

        // 2. subdomínio do domínio-base ({slug}.base)
        if ($base !== '' && str_ends_with($host, '.' . $base)) {
            $prefixo = substr($host, 0, -(strlen($base) + 1));
            $slug    = explode('.', $prefixo)[0] ?? '';
            if ($slug !== '' && ! in_array($slug, self::RESERVADOS, true)) {
                return static::query()->where('slug', $slug)->where('ativo', true)->first();
            }
        }

        // 3. fallback (apex / host legado) -> organizadora padrão
        if ($slug = config('certamme.default_tenant')) {
            if ($t = static::query()->where('slug', $slug)->where('ativo', true)->first()) {
                return $t;
            }
        }

        return static::query()->where('ativo', true)->first() ?? static::query()->first();
    }

    /** Cor da marca (hex). Fallback: verde esmeralda do Certamme. */
    public function corPrimaria(): string
    {
        return $this->cor_primaria ?: '#059669';
    }

    /** URL pública do logo, ou null. */
    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }
        return Storage::disk('public')->url($this->logo_path);
    }
}
