<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Pagamento;
use App\Models\Resultado;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_exporta_classificados_e_arrecadacao_csv(): void
    {
        $user = User::factory()->create();
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'Concurso X']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $i = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '00001', 'nome' => 'Ana Souza']);
        Resultado::create(['etapa_id' => $e->id, 'inscricao_id' => $i->id, 'total' => 8, 'acertos' => 8, 'classificacao' => 1]);
        $pag = Pagamento::create(['inscricao_id' => $i->id, 'valor' => 80, 'metodo' => 'pix', 'status' => 'pago']);
        Tenant::setCurrent(null);

        $r1 = $this->actingAs($user)->get("/relatorios/classificados/{$e->id}");
        $r1->assertOk();
        $this->assertStringContainsString('Ana Souza', $r1->getContent());

        $r2 = $this->actingAs($user)->get("/relatorios/arrecadacao/{$c->id}");
        $r2->assertOk();
        $this->assertStringContainsString('80,00', $r2->getContent());
    }
}
