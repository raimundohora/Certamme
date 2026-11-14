<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leituras_cartao', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prova_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->foreignId('importacao_id')->nullable()->constrained('importacoes')->nullOnDelete();
            $t->json('respostas');                 // {"1":"A","2":"C",...}
            $t->string('origem')->default('csv');  // leitora|csv|omr
            $t->timestamps();
            $t->unique(['prova_id', 'inscricao_id']); // idempotente: 1 leitura por candidato/prova
        });
    }
    public function down(): void { Schema::dropIfExists('leituras_cartao'); }
};
