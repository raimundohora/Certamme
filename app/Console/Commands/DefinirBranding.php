<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Define a identidade visual (white-label) de uma organizadora: nome, cor primária
 * (hex) e/ou logo (a partir de um arquivo local no servidor).
 */
class DefinirBranding extends Command
{
    protected $signature = 'certamme:branding
        {slug : Slug da organizadora}
        {--nome= : Novo nome de exibição}
        {--cor= : Cor primária em hex, ex. #1d4ed8}
        {--logo= : Caminho de um arquivo de imagem no servidor p/ usar como logo}';

    protected $description = 'Define nome, cor e logo (white-label) de uma organizadora.';

    public function handle(): int
    {
        $tenant = Tenant::where('slug', $this->argument('slug'))->first();
        if (! $tenant) {
            $this->error("Organizadora '{$this->argument('slug')}' não encontrada.");
            return self::FAILURE;
        }

        if ($this->option('nome')) {
            $tenant->nome = $this->option('nome');
        }

        if ($this->option('cor')) {
            $cor = trim($this->option('cor'));
            if (! preg_match('/^#?[0-9a-fA-F]{6}$/', $cor)) {
                $this->error('Cor inválida. Use hex, ex. #1d4ed8.');
                return self::FAILURE;
            }
            $tenant->cor_primaria = str_starts_with($cor, '#') ? $cor : "#{$cor}";
        }

        if ($this->option('logo')) {
            $src = $this->option('logo');
            if (! is_file($src)) {
                $this->error("Arquivo de logo não encontrado: {$src}");
                return self::FAILURE;
            }
            $ext  = pathinfo($src, PATHINFO_EXTENSION) ?: 'png';
            $dest = "logos/{$tenant->slug}." . strtolower($ext);
            Storage::disk('public')->put($dest, file_get_contents($src));
            $tenant->logo_path = $dest;
        }

        $tenant->save();

        $this->info("Identidade atualizada: {$tenant->nome} ({$tenant->slug})");
        $this->line('  Cor: ' . ($tenant->cor_primaria ?: 'padrão'));
        $this->line('  Logo: ' . ($tenant->logoUrl() ?: 'nenhum'));

        return self::SUCCESS;
    }
}
