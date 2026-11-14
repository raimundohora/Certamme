<?php
namespace Tests\Feature;

use App\Enums\SituacaoQuestao;
use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\GabaritoItem;
use App\Models\Prova;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class GabaritoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    private function novaProva(): Prova
    {
        $t = Tenant::create(['nome' => 'Banca', 'slug' => 'banca']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        return Prova::create(['etapa_id' => $e->id, 'nome' => 'Objetiva', 'caderno' => 'Tipo 1', 'qtd_questoes' => 10]);
    }

    public function test_gabarito_guarda_situacao_e_resposta_efetiva(): void
    {
        $p = $this->novaProva();
        $q1 = GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'disciplina' => 'Portugues', 'resposta_correta' => 'A']);
        $q2 = GabaritoItem::create(['prova_id' => $p->id, 'numero' => 2, 'disciplina' => 'Portugues', 'resposta_correta' => 'B', 'situacao' => SituacaoQuestao::ANULADA]);
        $q3 = GabaritoItem::create(['prova_id' => $p->id, 'numero' => 3, 'disciplina' => 'Matematica', 'resposta_correta' => 'C', 'situacao' => SituacaoQuestao::ALTERADA, 'resposta_nova' => 'D']);

        $q1->refresh(); $q2->refresh(); $q3->refresh();

        $this->assertSame(SituacaoQuestao::VALIDA, $q1->situacao);
        $this->assertSame('A', $q1->respostaEfetiva());
        $this->assertNull($q2->respostaEfetiva());        // anulada = ponto p/ todos
        $this->assertSame('D', $q3->respostaEfetiva());   // alterada usa resposta_nova
        $this->assertSame($p->tenant_id, $q1->tenant_id); // tenant preenchido sozinho
        $this->assertSame(3, $p->itens()->count());
    }

    public function test_alterada_exige_resposta_nova(): void
    {
        $p = $this->novaProva();
        $this->expectException(InvalidArgumentException::class);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'resposta_correta' => 'A', 'situacao' => SituacaoQuestao::ALTERADA]);
    }
}
