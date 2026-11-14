<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gabarito_itens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prova_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('numero');
            $t->string('disciplina')->nullable();
            $t->string('resposta_correta', 1)->nullable();
            $t->string('situacao')->default('valida');
            $t->string('resposta_nova', 1)->nullable();
            $t->decimal('peso', 6, 2)->default(1);
            $t->timestamps();
            $t->unique(['prova_id', 'numero']);
        });
    }
    public function down(): void { Schema::dropIfExists('gabarito_itens'); }
};
