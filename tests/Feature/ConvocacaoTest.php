<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Candidato;
use App\Models\Concurso;
use App\Models\Convocacao;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvocacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_convocacao_publicada_aparece_no_comprovante(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 2, 'tipo' => TipoEtapa::REDACAO]);
        $cand = Candidato::create(['nome' => 'F', 'cpf' => '33333333333', 'email' => 'cv@x.com', 'password' => 'segredo1']);
        $insc = Inscricao::create(['concurso_id' => $c->id, 'candidato_id' => $cand->id, 'numero_inscricao' => '1', 'nome' => 'F', 'status' => 'confirmada']);
        Convocacao::create(['etapa_id' => $e->id, 'inscricao_id' => $insc->id, 'publicado' => true, 'observacao' => 'Prova discursiva']);
        // nao publicada nao aparece
        Convocacao::create(['etapa_id' => $e->id, 'inscricao_id' => $insc->id, 'publicado' => false]);
        Tenant::setCurrent(null);

        $this->actingAs($cand, 'candidato')->get("/inscricao/{$insc->id}/comprovante")
            ->assertOk()->assertSee('CONVOCADO')->assertSee('Prova discursiva');
    }
}
