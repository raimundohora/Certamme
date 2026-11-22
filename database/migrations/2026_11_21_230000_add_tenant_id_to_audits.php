<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mapa auditable_type => tabela, usado para herdar o tenant_id do registro
     * auditado ao preencher as linhas já existentes de `audits`.
     */
    private array $mapa = [
        'App\\Models\\Nota'         => 'notas',
        'App\\Models\\Resultado'    => 'resultados',
        'App\\Models\\GabaritoItem' => 'gabarito_items',
        'App\\Models\\Pagamento'    => 'pagamentos',
        'App\\Models\\Recurso'      => 'recursos',
    ];

    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->index('tenant_id');
        });

        // Backfill: herda o tenant do registro auditado (UPDATE ... FROM do Postgres).
        foreach ($this->mapa as $tipo => $tabela) {
            if (! Schema::hasTable($tabela) || ! Schema::hasColumn($tabela, 'tenant_id')) {
                continue;
            }
            DB::statement(
                "UPDATE audits a SET tenant_id = t.tenant_id
                 FROM {$tabela} t
                 WHERE a.auditable_type = ? AND a.auditable_id = t.id AND a.tenant_id IS NULL",
                [$tipo]
            );
        }

        // Linhas cujo registro auditado já foi apagado: cai no primeiro tenant.
        $fallback = DB::table('tenants')->orderBy('id')->value('id');
        if ($fallback) {
            DB::table('audits')->whereNull('tenant_id')->update(['tenant_id' => $fallback]);
        }
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
