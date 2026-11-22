<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * White-label por organizadora: cor primária (hex) e logo. Vazios => usa o
 * padrão do Certamme (verde esmeralda), preservando o visual atual.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('cor_primaria', 9)->nullable()->after('dominio');
            $table->string('logo_path')->nullable()->after('cor_primaria');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['cor_primaria', 'logo_path']);
        });
    }
};
