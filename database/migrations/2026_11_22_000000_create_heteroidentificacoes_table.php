<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heteroidentificacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $table->string('status')->default('pendente');
            $table->text('parecer')->nullable();
            $table->string('avaliador')->nullable();
            $table->timestamp('data_avaliacao')->nullable();
            $table->timestamps();

            $table->unique('inscricao_id'); // uma verificação por inscrição
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heteroidentificacoes');
    }
};
