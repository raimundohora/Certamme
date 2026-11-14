<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documentos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->string('tipo')->default('outros');
            $t->string('arquivo')->nullable();
            $t->string('status')->default('pendente');
            $t->string('observacao')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('documentos'); }
};
