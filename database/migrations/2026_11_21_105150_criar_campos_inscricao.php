<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campos_inscricao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('concurso_id')->constrained('concursos')->cascadeOnDelete();
            $table->string('rotulo');
            $table->string('chave')->nullable();
            $table->string('tipo')->default('texto');
            $table->json('opcoes')->nullable();
            $table->boolean('obrigatorio')->default(false);
            $table->boolean('visivel')->default(true);
            $table->integer('ordem')->default(0);
            $table->string('ajuda')->nullable();
            $table->timestamps();
        });

        Schema::table('inscricoes', function (Blueprint $table) {
            $table->json('campos_extras')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropColumn('campos_extras');
        });
        Schema::dropIfExists('campos_inscricao');
    }
};
