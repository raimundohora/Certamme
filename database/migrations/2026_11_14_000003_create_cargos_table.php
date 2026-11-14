<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cargos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained()->cascadeOnDelete();
            $t->string('nome');
            $t->unsignedInteger('vagas')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cargos'); }
};
