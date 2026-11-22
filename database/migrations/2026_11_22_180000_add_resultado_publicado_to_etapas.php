<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Publicação da classificação por etapa: enquanto false, o resultado NÃO aparece no
 * portal público (evita vazar antes da divulgação oficial). O admin publica/despublica.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etapas', function (Blueprint $table) {
            $table->boolean('resultado_publicado')->default(false);
            $table->timestamp('resultado_publicado_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('etapas', function (Blueprint $table) {
            $table->dropColumn(['resultado_publicado', 'resultado_publicado_em']);
        });
    }
};
