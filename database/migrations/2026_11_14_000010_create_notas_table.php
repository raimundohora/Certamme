<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->string('disciplina')->nullable();
            $t->unsignedInteger('acertos')->default(0);
            $t->decimal('nota_bruta', 8, 2)->default(0);
            $t->decimal('nota_final', 8, 2)->default(0);
            $t->boolean('eliminado')->default(false);
            $t->timestamp('calculada_em')->nullable();
            $t->timestamps();
            $t->unique(['inscricao_id', 'etapa_id', 'disciplina']);
        });
    }
    public function down(): void { Schema::dropIfExists('notas'); }
};
