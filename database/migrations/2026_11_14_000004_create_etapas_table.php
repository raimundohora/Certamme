<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('etapas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('concurso_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cargo_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedSmallInteger('ordem')->default(1);
            $t->string('tipo');
            $t->boolean('eliminatoria')->default(false);
            $t->boolean('classificatoria')->default(true);
            $t->decimal('nota_minima', 6, 2)->nullable();
            $t->decimal('peso', 6, 2)->default(1);
            $t->json('config')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('etapas'); }
};
