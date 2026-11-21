<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cargos', function (Blueprint $t) {
            // vagas reservadas por cota: {"cota_pcd":1,"cota_racial":1,"cota_indigena":0}
            // ampla = vagas (total) - soma das reservadas
            $t->json('vagas_cotas')->nullable()->after('vagas');
        });
    }
    public function down(): void {
        Schema::table('cargos', function (Blueprint $t) {
            $t->dropColumn('vagas_cotas');
        });
    }
};
