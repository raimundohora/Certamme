<?php
namespace Tests\Feature;

use App\Enums\StatusInscricao;
use App\Enums\TipoEtapa;
use App\Models\Alocacao;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\LocalProva;
use App\Models\Sala;
use App\Models\Tenant;
use App\Services\EnsalamentoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsalamentoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_distribui_nas_salas_respeitando_capacidade_e_ordem(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);

        $local = LocalProva::create(['concurso_id' => $c->id, 'nome' => 'Escola Centro', 'cidade' => 'Natal']);
        $sala1 = Sala::create(['local_prova_id' => $local->id, 'nome' => 'Sala 01', 'capacidade' => 2]);
        $sala2 = Sala::create(['local_prova_id' => $local->id, 'nome' => 'Sala 02', 'capacidade' => 3]);

        // 5 confirmados (nomes fora de ordem p/ testar ordenacao)
        foreach (['Carla', 'Ana', 'Elaine', 'Bruno', 'Diego'] as $i => $nome) {
            Inscricao::create([
                'concurso_id' => $c->id, 'numero_inscricao' => (string) (100 + $i),
                'nome' => $nome, 'status' => StatusInscricao::CONFIRMADA,
            ]);
        }

        $n = app(EnsalamentoService::class)->alocar($e);
        $this->assertSame(5, $n);

        // Sala 1 (cap 2): Ana(1), Bruno(2)  |  Sala 2 (cap 3): Carla(1), Diego(2), Elaine(3)
        $this->assertSame(2, Alocacao::where('sala_id', $sala1->id)->count());
        $this->assertSame(3, Alocacao::where('sala_id', $sala2->id)->count());

        $ana = Alocacao::whereHas('inscricao', fn ($q) => $q->where('nome', 'Ana'))->first();
        $this->assertSame($sala1->id, $ana->sala_id);
        $this->assertSame(1, $ana->carteira); // 1a por ordem alfabetica

        $elaine = Alocacao::whereHas('inscricao', fn ($q) => $q->where('nome', 'Elaine'))->first();
        $this->assertSame($sala2->id, $elaine->sala_id);
        $this->assertSame(3, $elaine->carteira);
    }
}
