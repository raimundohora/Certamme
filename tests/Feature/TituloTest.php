<?php
namespace Tests\Feature;

use App\Enums\StatusTitulo;
use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Tenant;
use App\Models\Titulo;
use App\Models\TituloTipo;
use App\Services\TituloService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class TituloTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_avaliar_limita_ao_teto_depois_confere_e_audita(): void
    {
        config(['audit.console' => true]);

        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 3, 'tipo' => TipoEtapa::TITULOS]);
        $tipo = TituloTipo::create(['etapa_id' => $e->id, 'nome' => 'Doutorado', 'pontuacao_maxima' => 10]);
        $insc = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1']);
        $titulo = Titulo::create(['inscricao_id' => $insc->id, 'titulo_tipo_id' => $tipo->id]);
        $titulo->refresh();

        $this->assertSame(StatusTitulo::PENDENTE, $titulo->status);

        // avalia com 15 -> limita ao teto 10
        app(TituloService::class)->avaliar($titulo, 15, 'banca@x');
        $titulo->refresh();
        $this->assertSame('10.00', $titulo->pontos);
        $this->assertSame(StatusTitulo::AVALIADO, $titulo->status);
        $this->assertSame('banca@x', $titulo->avaliador);

        // confere (duplo-check)
        app(TituloService::class)->conferir($titulo, 'conferente@x');
        $titulo->refresh();
        $this->assertSame(StatusTitulo::CONFERIDO, $titulo->status);
        $this->assertSame('conferente@x', $titulo->conferente);

        $this->assertTrue(Audit::where('auditable_type', Titulo::class)->where('auditable_id', $titulo->id)->exists());
    }
}
