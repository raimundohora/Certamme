<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Onboarding de uma nova organizadora (tenant): cria a organizadora e o seu
 * primeiro administrador. A organizadora nasce com os dados vazios — o
 * isolamento é lógico (tenant_id), não um banco novo.
 */
class NovaOrganizadora extends Command
{
    protected $signature = 'certamme:nova-organizadora
        {nome : Nome da organizadora}
        {--slug= : Subdomínio (gerado a partir do nome se omitido)}
        {--dominio= : Domínio próprio do cliente (opcional, ex. concursos.prefeitura.gov.br)}
        {--admin-nome= : Nome do administrador}
        {--admin-email= : E-mail de login do administrador}
        {--admin-senha= : Senha inicial do administrador}';

    protected $description = 'Cria uma nova organizadora (tenant) e o seu primeiro administrador.';

    public function handle(): int
    {
        $nome = (string) $this->argument('nome');
        $slug = Str::slug($this->option('slug') ?: $nome);

        if ($slug === '') {
            $this->error('Slug inválido.');
            return self::FAILURE;
        }
        if (Tenant::where('slug', $slug)->exists()) {
            $this->error("Já existe organizadora com o slug '{$slug}'.");
            return self::FAILURE;
        }

        $dominio    = $this->option('dominio') ? strtolower(trim($this->option('dominio'))) : null;
        $adminNome  = $this->option('admin-nome') ?: ($this->input->isInteractive() ? $this->ask('Nome do administrador') : 'Administrador');
        $adminEmail = $this->option('admin-email') ?: ($this->input->isInteractive() ? $this->ask('E-mail do administrador') : null);
        $adminSenha = $this->option('admin-senha') ?: ($this->input->isInteractive() ? $this->secret('Senha inicial') : Str::password(12));

        if (! $adminEmail) {
            $this->error('Informe o e-mail do administrador (--admin-email).');
            return self::FAILURE;
        }
        if (User::where('email', $adminEmail)->exists()) {
            $this->error("Já existe usuário com o e-mail '{$adminEmail}'.");
            return self::FAILURE;
        }

        $tenant = Tenant::create([
            'nome'    => $nome,
            'slug'    => $slug,
            'dominio' => $dominio,
            'ativo'   => true,
        ]);

        // garante que o admin nasça vinculado a ESTA organizadora, mesmo com escopo global ativo
        Tenant::setCurrent($tenant);

        $user = new User();
        $user->tenant_id = $tenant->id;
        $user->name      = $adminNome;
        $user->email     = $adminEmail;
        $user->password  = Hash::make($adminSenha);
        $user->save();

        // concede acesso total DENTRO da organizadora (dados já são isolados por tenant_id)
        try {
            if (method_exists($user, 'assignRole') && \Spatie\Permission\Models\Role::where('name', 'super_admin')->exists()) {
                $user->assignRole('super_admin');
            }
        } catch (\Throwable $e) {
            $this->warn('Papel super_admin não atribuído: ' . $e->getMessage());
        }

        $base = config('certamme.base_domain');
        $url  = $dominio ? "https://{$dominio}" : "https://{$slug}.{$base}";

        $this->info("Organizadora criada: {$nome} (#{$tenant->id})");
        $this->line("  Portal/Admin: {$url}  (admin em {$url}/admin)");
        $this->line("  Admin: {$adminEmail}");
        if (! $this->option('admin-senha')) {
            $this->line("  Senha inicial: {$adminSenha}");
        }

        return self::SUCCESS;
    }
}
