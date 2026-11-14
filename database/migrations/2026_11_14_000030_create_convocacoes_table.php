<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('convocacoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->boolean('publicado')->default(false);
            $t->string('observacao')->nullable();
            $t->timestamps();
            $t->unique(['etapa_id', 'inscricao_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('convocacoes'); }
};
