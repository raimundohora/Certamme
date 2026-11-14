<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('provas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained()->cascadeOnDelete();
            $t->string('nome');
            $t->string('caderno')->nullable();      // ex.: "Tipo 1"
            $t->unsignedSmallInteger('qtd_questoes')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('provas'); }
};
