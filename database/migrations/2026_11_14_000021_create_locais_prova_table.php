<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('locais_prova', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained('concursos')->cascadeOnDelete();
            $t->string('nome');                 // ex.: "Escola Municipal Centro"
            $t->string('endereco')->nullable();
            $t->string('cidade')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('locais_prova'); }
};
