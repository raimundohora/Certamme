<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('local_prova_id')->constrained('locais_prova')->cascadeOnDelete();
            $t->string('nome');                 // ex.: "Sala 01"
            $t->unsignedSmallInteger('capacidade')->default(30);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('salas'); }
};
