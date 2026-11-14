<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagamentos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $t->decimal('valor', 8, 2)->default(0);
            $t->string('metodo')->default('pix');
            $t->string('status')->default('pendente');
            $t->string('external_id')->nullable()->index();  // id da fatura no gateway (Iugu)
            $t->string('secure_url')->nullable();
            $t->string('linha_digitavel')->nullable();
            $t->text('pix_qrcode')->nullable();
            $t->timestamp('pago_em')->nullable();
            $t->timestamps();
            $t->unique('inscricao_id');
        });
    }
    public function down(): void { Schema::dropIfExists('pagamentos'); }
};
