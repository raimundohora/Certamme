<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('importacoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prova_id')->constrained()->cascadeOnDelete();
            $t->string('arquivo')->nullable();
            $t->unsignedInteger('total')->default(0);
            $t->unsignedInteger('importados')->default(0);
            $t->unsignedInteger('erros')->default(0);
            $t->json('erros_detalhe')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('importacoes'); }
};
