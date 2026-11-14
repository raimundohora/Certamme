<?php
namespace Tests\Feature;

use App\Enums\StatusSubjetiva;
use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\ProvaSubjetiva;
use App\Models\Tenant;
use App\Services\SubjetivaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjetivaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    private function novaProva(): ProvaSubjetiva
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 2, 'tipo' => TipoEtapa::REDACAO]);
        $i = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1']);
        return ProvaSubjetiva::create(['etapa_id' => $e->id, 'inscricao_id' => $i->id]);
    }

    public function test_prova_e_anonima_e_duas_correcoes_proximas_geram_media(): void
    {
        $ps = $this->novaProva();
        $ps->refresh();
        $this->assertNotEmpty($ps->codigo_controle);   // anonimizacao (corretor ve o codigo, nao o candidato)

        $svc = app(SubjetivaService::class);
        $svc->adicionarCorrecao($ps, 'corretor-1', 8.0);
        $svc->adicionarCorrecao($ps, 'corretor-2', 9.0);
        $svc->consolidar($ps, 2.0);

        $ps->refresh();
        $this->assertSame(StatusSubjetiva::CORRIGIDA, $ps->status);
        $this->assertSame('8.50', $ps->nota_final);
    }

    public function test_divergencia_vira_divergente_e_terceiro_resolve(): void
    {
        $ps = $this->novaProva();
        $svc = app(SubjetivaService::class);

        $svc->adicionarCorrecao($ps, 'c1', 5.0);
        $svc->adicionarCorrecao($ps, 'c2', 9.0);   // diferenca 4 > 2 => divergente
        $svc->consolidar($ps, 2.0);
        $ps->refresh();
        $this->assertSame(StatusSubjetiva::DIVERGENTE, $ps->status);
        $this->assertNull($ps->nota_final);

        $svc->adicionarCorrecao($ps, 'c3', 5.5);   // 3a correcao, proxima da c1
        $svc->consolidar($ps, 2.0);
        $ps->refresh();
        $this->assertSame(StatusSubjetiva::CORRIGIDA, $ps->status);
        $this->assertSame('5.25', $ps->nota_final); // media das 2 mais proximas (5.0 e 5.5)
    }
}
