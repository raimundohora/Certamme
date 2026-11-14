<?php
namespace Tests\Feature;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Pagamento;
use App\Models\Tenant;
use App\Services\PagamentoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class PagamentoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_cria_pendente_e_concilia_por_webhook_idempotente(): void
    {
        config(['audit.console' => true]);

        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $insc = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1']);
        $svc = app(PagamentoService::class);

        $pag = $svc->criar($insc, 85.00, MetodoPagamento::PIX);
        $pag->refresh();
        $this->assertSame(StatusPagamento::PENDENTE, $pag->status);
        $this->assertSame('85.00', $pag->valor);

        // o gateway devolveu um id de fatura
        $pag->update(['external_id' => 'iugu_inv_123']);

        // webhook confirma
        $ok = $svc->confirmarPorExternalId('iugu_inv_123');
        $this->assertSame(StatusPagamento::PAGO, $ok->status);
        $this->assertNotNull($ok->pago_em);

        // idempotente (nao quebra se vier 2x)
        $again = $svc->confirmarPorExternalId('iugu_inv_123');
        $this->assertSame(StatusPagamento::PAGO, $again->status);

        // fatura desconhecida -> null (nao cria nada)
        $this->assertNull($svc->confirmarPorExternalId('nao_existe'));

        // auditado (dinheiro)
        $this->assertTrue(Audit::where('auditable_type', Pagamento::class)->exists());
    }
}
