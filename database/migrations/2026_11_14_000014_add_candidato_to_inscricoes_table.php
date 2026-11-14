<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inscricoes', function (Blueprint $t) {
            $t->foreignId('candidato_id')->nullable()->after('concurso_id')->constrained('candidatos')->nullOnDelete();
            $t->string('modalidade')->default('ampla');
            $t->string('status')->default('pendente');
        });
    }
    public function down(): void {
        Schema::table('inscricoes', function (Blueprint $t) {
            $t->dropConstrainedForeignId('candidato_id');
            $t->dropColumn(['modalidade', 'status']);
        });
    }
};
