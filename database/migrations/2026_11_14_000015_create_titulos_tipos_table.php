<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('titulos_tipos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('etapa_id')->constrained('etapas')->cascadeOnDelete();
            $t->string('nome');                        // ex.: "Doutorado", "Curso >= 40h"
            $t->decimal('pontuacao_maxima', 6, 2)->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('titulos_tipos'); }
};
