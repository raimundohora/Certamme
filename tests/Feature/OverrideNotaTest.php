<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\GabaritoItem;
use App\Models\Inscricao;
use App\Models\LeituraCartao;
use App\Models\Nota;
use App\Models\Prova;
use App\Models\Tenant;
use App\Services\CalcularNotaService;
use App\Services\OverrideNotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class OverrideNotaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_override_manual_persiste_contra_recalculo_e_e_auditado(): void
    {
        config(['audit.console' => true]);

        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $p = Prova::create(['etapa_id' => $e->id, 'nome' => 'Obj', 'qtd_questoes' => 1]);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'disciplina' => 'Geral', 'resposta_correta' => 'A']);
        $ana = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1']);
        LeituraCartao::create(['prova_id' => $p->id, 'inscricao_id' => $ana->id, 'respostas' => ['1' => 'A']]);

        app(CalcularNotaService::class)->calcular($p);
        $nota = Nota::where('inscricao_id', $ana->id)->first();
        $this->assertSame(1, $nota->acertos);
        $this->assertSame('1.00', $nota->nota_final);

        // override manual (recurso deferido)
        app(OverrideNotaService::class)->aplicar($nota, 9.5, 'Recurso deferido - questao fora do edital', 'avaliador@banca');
        $nota->refresh();
        $this->assertTrue($nota->override);
        $this->assertSame('9.50', $nota->nota_final);
        $this->assertSame('Recurso deferido - questao fora do edital', $nota->override_motivo);

        // recalculo NAO pode apagar o override
        app(CalcularNotaService::class)->calcular($p);
        $nota->refresh();
        $this->assertSame('9.50', $nota->nota_final);
        $this->assertTrue($nota->override);

        // auditado
        $this->assertTrue(Audit::where('auditable_type', Nota::class)->where('auditable_id', $nota->id)->exists());
    }
}
