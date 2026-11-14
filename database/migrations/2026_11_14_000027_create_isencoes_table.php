<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('isencoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->string('nis')->nullable();          // NIS/CadUnico
            $t->string('justificativa')->nullable();
            $t->string('status')->default('pendente');
            $t->string('motivo')->nullable();        // motivo do indeferimento
            $t->timestamps();
            $t->unique('inscricao_id');              // 1 pedido de isencao por inscricao
        });
    }
    public function down(): void { Schema::dropIfExists('isencoes'); }
};
