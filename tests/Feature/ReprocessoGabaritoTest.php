<?php
namespace Tests\Feature;

use App\Enums\SituacaoQuestao;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class ReprocessoGabaritoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_reprocessa_notas_e_audita_ao_anular_questao(): void
    {
        config(['audit.console' => true]);
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $p = Prova::create(['etapa_id' => $e->id, 'nome' => 'Obj', 'qtd_questoes' => 2]);

        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'disciplina' => 'Geral', 'resposta_correta' => 'A']);
        $q2 = GabaritoItem::create(['prova_id' => $p->id, 'numero' => 2, 'disciplina' => 'Geral', 'resposta_correta' => 'B']);

        $ana = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1', 'nome' => 'Ana']);
        LeituraCartao::create(['prova_id' => $p->id, 'inscricao_id' => $ana->id, 'respostas' => ['1'=>'A','2'=>'Z']]);

        app(CalcularNotaService::class)->calcular($p);
        $nota = Nota::where('inscricao_id', $ana->id)->first();
        $this->assertSame(1, $nota->acertos);

        $q2->update(['situacao' => SituacaoQuestao::ANULADA]);

        $nota->refresh();
        $this->assertSame(2, $nota->acertos);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => GabaritoItem::class,
            'auditable_id'   => $q2->id,
            'event'          => 'updated',
        ]);
        $audit = Audit::where('auditable_id', $q2->id)->where('event', 'updated')->latest('id')->first();
        $this->assertArrayHasKey('situacao', $audit->getModified());
    }
}
