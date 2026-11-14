<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('correcoes_subjetivas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prova_subjetiva_id')->constrained('provas_subjetivas')->cascadeOnDelete();
            $t->string('corretor');
            $t->decimal('nota', 6, 2);
            $t->json('quesitos')->nullable();               // nota por quesito/topico (rubrica)
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('correcoes_subjetivas'); }
};
