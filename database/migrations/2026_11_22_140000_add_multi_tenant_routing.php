<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Roteamento multi-tenant por subdomínio/domínio próprio:
 *  - tenants.dominio: domínio próprio do cliente (CNAME), ex. concursos.prefeitura.gov.br;
 *  - users.tenant_id: organizadora do admin. NULL = super-admin da plataforma
 *    (acessa qualquer subdomínio); preenchido = admin restrito à própria organizadora.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('dominio')->nullable()->unique()->after('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['dominio']);
            $table->dropColumn('dominio');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
