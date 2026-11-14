<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Minimo (stub do Bloco A). So o necessario p/ o motor de avaliacao casar leituras.
        Schema::create('inscricoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cargo_id')->nullable()->constrained()->nullOnDelete();
            $t->string('numero_inscricao');
            $t->string('nome')->nullable();
            $t->timestamps();
            $t->unique(['concurso_id', 'numero_inscricao']);
        });
    }
    public function down(): void { Schema::dropIfExists('inscricoes'); }
};
