<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Certificados/documentos oficiais emitidos a partir de uma inscrição
 * (comprovante de inscrição, declaração de participação, certificado de classificação).
 * Cada documento recebe um código de autenticação público e um hash (fingerprint
 * anti-adulteração) do snapshot dos dados no momento da emissão — conferível em
 * /autenticar/{codigo}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $table->string('tipo', 40);
            $table->string('codigo', 24)->unique();
            $table->string('hash', 64);
            $table->json('dados');
            $table->timestamp('emitido_em');
            $table->timestamps();

            $table->unique(['inscricao_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
