<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('anexos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained('concursos')->cascadeOnDelete();
            $t->string('titulo');
            $t->string('grupo')->default('edital');
            $t->string('arquivo')->nullable();     // caminho no storage
            $t->boolean('publicado')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('anexos'); }
};
