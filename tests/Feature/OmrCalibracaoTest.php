<?php
namespace Tests\Feature;

use App\Enums\FormatoMarcacao;
use App\Enums\IdentificacaoOmr;
use App\Enums\OpcoesOmr;
use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\OmrCalibracao;
use App\Models\Prova;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OmrCalibracaoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_calibracao_guarda_parametros_do_cartao(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $p = Prova::create(['etapa_id' => $e->id, 'nome' => 'Obj']);

        $cal = OmrCalibracao::create([
            'prova_id' => $p->id,
            'identificacao' => IdentificacaoOmr::QR,
            'formato_marcacao' => FormatoMarcacao::CIRCULAR,
            'threshold' => 45,
            'qtde_guias' => 2,
            'opcoes' => OpcoesOmr::A_E,
        ]);
        $cal->refresh();

        $this->assertSame($t->id, $cal->tenant_id);
        $this->assertSame(IdentificacaoOmr::QR, $cal->identificacao);
        $this->assertSame(FormatoMarcacao::CIRCULAR, $cal->formato_marcacao);
        $this->assertSame(45, $cal->threshold);
        $this->assertSame(2, $cal->qtde_guias);
        $this->assertTrue($cal->ignorar_duplicados); // default
    }
}
