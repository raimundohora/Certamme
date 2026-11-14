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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GabaritoExclusivoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_corrigir_leitura_do_candidato_recalcula_a_nota(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $p = Prova::create(['etapa_id' => $e->id, 'nome' => 'Obj', 'qtd_questoes' => 1]);
        GabaritoItem::create(['prova_id' => $p->id, 'numero' => 1, 'disciplina' => 'Geral', 'resposta_correta' => 'A']);
        $i = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1']);
        // leitura errada (marcou B)
        $leitura = LeituraCartao::create(['prova_id' => $p->id, 'inscricao_id' => $i->id, 'respostas' => ['1' => 'B']]);

        app(CalcularNotaService::class)->calcular($p);
        $this->assertSame(0, Nota::where('inscricao_id', $i->id)->first()->acertos);

        // gabarito-exclusivo: conserta a leitura (era A)
        $leitura->update(['respostas' => ['1' => 'A']]);

        $this->assertSame(1, Nota::where('inscricao_id', $i->id)->first()->fresh()->acertos);
    }
}
