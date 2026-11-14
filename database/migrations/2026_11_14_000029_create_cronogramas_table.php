<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cronogramas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained('concursos')->cascadeOnDelete();
            $t->string('evento');               // ex.: "Inscricoes", "Prova objetiva", "Resultado final"
            $t->date('data')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cronogramas'); }
};
