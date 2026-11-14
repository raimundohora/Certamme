<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alocacoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->foreignId('sala_id')->constrained('salas')->cascadeOnDelete();
            $t->unsignedInteger('carteira');
            $t->timestamps();
            $t->unique(['etapa_id', 'inscricao_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('alocacoes'); }
};
