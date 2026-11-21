<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('resultados', function (Blueprint $t) {
            // aprovado | classificavel | eliminado (null = ainda nao apurado)
            $t->string('situacao')->nullable()->after('eliminado');
        });
    }
    public function down(): void {
        Schema::table('resultados', function (Blueprint $t) {
            $t->dropColumn('situacao');
        });
    }
};
