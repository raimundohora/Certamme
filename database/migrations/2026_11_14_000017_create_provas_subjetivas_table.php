<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('provas_subjetivas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->string('codigo_controle')->unique();        // anonimizacao: corretor ve isso, nao o candidato
            $t->decimal('nota_final', 6, 2)->nullable();
            $t->string('status')->default('pendente');
            $t->timestamps();
            $t->unique(['etapa_id', 'inscricao_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('provas_subjetivas'); }
};
