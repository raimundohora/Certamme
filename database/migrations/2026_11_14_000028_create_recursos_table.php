<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('recursos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->foreignId('etapa_id')->nullable()->constrained('etapas')->nullOnDelete();
            $t->string('tipo')->default('gabarito');
            $t->unsignedSmallInteger('questao')->nullable();
            $t->text('texto');
            $t->string('status')->default('pendente');
            $t->text('resposta')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('recursos'); }
};
