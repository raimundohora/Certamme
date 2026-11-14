<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('candidatos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->string('nome');
            $t->string('cpf', 14);
            $t->string('email')->nullable();
            $t->date('data_nascimento')->nullable();
            $t->string('telefone')->nullable();
            $t->timestamps();
            $t->unique(['tenant_id', 'cpf']);
        });
    }
    public function down(): void { Schema::dropIfExists('candidatos'); }
};
