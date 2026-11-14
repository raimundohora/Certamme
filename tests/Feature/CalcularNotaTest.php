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
use Tests\TestCase;

class CalcularNotaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_calcula_nota_com_anulada_alterada_e_nota_minima(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA, 'nota_minima' => 3]);
        $p = Prova::create(['etapa_id' => $e->id, 'nome' => 'Obj', 'qtd_questoes' => 5]);

        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'disciplina' => 'Portugues', 'resposta_correta' => 'A']);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 2, 'disciplina' => 'Portugues', 'resposta_correta' => 'B']);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 3, 'disciplina' => 'Portugues', 'resposta_correta' => 'C', 'situacao' => SituacaoQuestao::ANULADA]);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 4, 'disciplina' => 'Matematica', 'resposta_correta' => 'C', 'situacao' => SituacaoQuestao::ALTERADA, 'resposta_nova' => 'D']);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 5, 'disciplina' => 'Matematica', 'resposta_correta' => 'E']);

        $ana = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1001', 'nome' => 'Ana']);
        $bia = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1002', 'nome' => 'Bia']);

        LeituraCartao::create(['prova_id' => $p->id, 'inscricao_id' => $ana->id, 'respostas' => ['1'=>'A','2'=>'X','3'=>'Z','4'=>'D','5'=>'E']]);
        LeituraCartao::create(['prova_id' => $p->id, 'inscricao_id' => $bia->id, 'respostas' => ['1'=>'A','2'=>'A','3'=>'A','4'=>'A','5'=>'A']]);

        app(CalcularNotaService::class)->calcular($p);

        // Ana: Port = q1 + q3(anulada) = 2 ; Mat = q4(alterada D) + q5 = 2 ; total 4 >= 3 => nao eliminada
        $anaPort = Nota::where('inscricao_id', $ana->id)->where('disciplina', 'Portugues')->first();
        $anaMat  = Nota::where('inscricao_id', $ana->id)->where('disciplina', 'Matematica')->first();
        $this->assertSame(2, $anaPort->acertos);
        $this->assertSame('2.00', $anaPort->nota_final);
        $this->assertSame(2, $anaMat->acertos);
        $this->assertFalse($anaPort->eliminado);

        // Bia: Port = q1 + q3(anulada) = 2 ; Mat = 0 ; total 2 < 3 => eliminada
        $biaPort = Nota::where('inscricao_id', $bia->id)->where('disciplina', 'Portugues')->first();
        $biaMat  = Nota::where('inscricao_id', $bia->id)->where('disciplina', 'Matematica')->first();
        $this->assertSame(2, $biaPort->acertos);
        $this->assertSame(0, $biaMat->acertos);
        $this->assertTrue($biaMat->eliminado);
        $this->assertTrue($biaPort->eliminado);
    }
}
