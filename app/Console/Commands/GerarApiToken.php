<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Gera um token de API para uma organizadora. O token em claro é exibido
 * uma única vez; depois só fica o hash.
 */
class GerarApiToken extends Command
{
    protected $signature = 'certamme:api-token
        {slug : Slug da organizadora}
        {--nome=API : Nome/identificação do token}';

    protected $description = 'Gera um token de API (bearer) para uma organizadora.';

    public function handle(): int
    {
        $tenant = Tenant::where('slug', $this->argument('slug'))->first();
        if (! $tenant) {
            $this->error("Organizadora '{$this->argument('slug')}' não encontrada.");
            return self::FAILURE;
        }

        $claro   = 'cert_' . Str::random(40);
        $prefixo = substr($claro, 0, 12);

        ApiToken::create([
            'tenant_id'  => $tenant->id,
            'nome'       => (string) $this->option('nome'),
            'prefixo'    => $prefixo,
            'token_hash' => hash('sha256', $claro),
        ]);

        $this->info("Token gerado para {$tenant->nome} ({$tenant->slug}).");
        $this->newLine();
        $this->line('  ' . $claro);
        $this->newLine();
        $this->warn('Guarde agora — este token NÃO será exibido novamente.');
        $this->line('Uso: curl -H "Authorization: Bearer <token>" https://<host>/api/v1/concursos');

        return self::SUCCESS;
    }
}
