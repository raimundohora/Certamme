<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('candidatos', function (Blueprint $t) {
            $t->string('password')->nullable()->after('telefone');
            $t->rememberToken();
            // cadastro completo (padrao ProSeleta)
            $t->string('sexo')->nullable();
            $t->string('nome_social')->nullable();
            $t->string('nome_mae')->nullable();
            $t->string('rg')->nullable();
            $t->string('cep', 9)->nullable();
            $t->string('logradouro')->nullable();
            $t->string('numero', 20)->nullable();
            $t->string('bairro')->nullable();
            $t->string('cidade')->nullable();
            $t->string('uf', 2)->nullable();
            $t->boolean('pcd')->default(false);
            $t->string('tipo_deficiencia')->nullable();
        });
    }
    public function down(): void {
        Schema::table('candidatos', function (Blueprint $t) {
            $t->dropColumn(['password', 'remember_token', 'sexo', 'nome_social', 'nome_mae', 'rg',
                'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'uf', 'pcd', 'tipo_deficiencia']);
        });
    }
};
