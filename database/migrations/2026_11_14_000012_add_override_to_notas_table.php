<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notas', function (Blueprint $t) {
            $t->boolean('override')->default(false);
            $t->decimal('nota_override', 8, 2)->nullable();
            $t->string('override_motivo')->nullable();
            $t->string('override_por')->nullable();
        });
    }
    public function down(): void {
        Schema::table('notas', function (Blueprint $t) {
            $t->dropColumn(['override', 'nota_override', 'override_motivo', 'override_por']);
        });
    }
};
