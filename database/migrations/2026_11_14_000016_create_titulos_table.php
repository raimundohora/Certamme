<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('titulos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->foreignId('titulo_tipo_id')->constrained('titulos_tipos')->cascadeOnDelete();
            $t->decimal('pontos', 6, 2)->default(0);
            $t->string('status')->default('pendente');
            $t->string('avaliador')->nullable();
            $t->string('conferente')->nullable();
            $t->timestamps();
            $t->unique(['inscricao_id', 'titulo_tipo_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('titulos'); }
};
