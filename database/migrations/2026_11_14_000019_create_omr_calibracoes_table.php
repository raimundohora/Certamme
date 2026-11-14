<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('omr_calibracoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prova_id')->constrained('provas')->cascadeOnDelete();
            $t->string('identificacao')->default('codigo_barras'); // como identificar o candidato no cartao
            $t->unsignedSmallInteger('id_chars')->default(10);     // nº de caracteres do identificador
            $t->string('formato_marcacao')->default('retangular');
            $t->unsignedSmallInteger('threshold')->default(30);    // % preenchido p/ contar (10-80)
            $t->unsignedSmallInteger('qtde_guias')->default(3);    // fiduciais (2 ou 3)
            $t->string('opcoes')->default('a_e');
            $t->boolean('ignorar_duplicados')->default(true);
            $t->boolean('usar_codigo_prova')->default(false);
            $t->timestamps();
            $t->unique('prova_id');
        });
    }
    public function down(): void { Schema::dropIfExists('omr_calibracoes'); }
};
