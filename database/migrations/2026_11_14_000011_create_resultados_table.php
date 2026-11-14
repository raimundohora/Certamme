<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('resultados', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->decimal('total', 8, 2)->default(0);
            $t->unsignedInteger('acertos')->default(0);
            $t->boolean('eliminado')->default(false);
            $t->unsignedInteger('classificacao')->nullable();
            $t->timestamp('calculado_em')->nullable();
            $t->timestamps();
            $t->unique(['etapa_id', 'inscricao_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('resultados'); }
};
