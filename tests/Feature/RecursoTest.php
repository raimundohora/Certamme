<?php
namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecursoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_candidato_interpoe_recurso(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X', 'ano' => 2026, 'situacao' => 'aberto']);
        $cargo = Cargo::create(['concurso_id' => $c->id, 'nome' => 'A', 'vagas' => 1]);
        $cand = Candidato::create(['nome' => 'F', 'cpf' => '22222222222', 'email' => 'r@x.com', 'password' => 'segredo1']);
        $insc = Inscricao::create([
            'concurso_id' => $c->id, 'candidato_id' => $cand->id, 'cargo_id' => $cargo->id,
            'numero_inscricao' => '00001', 'nome' => 'F', 'modalidade' => 'ampla', 'status' => 'pendente',
        ]);
        Tenant::setCurrent(null);

        $this->actingAs($cand, 'candidato')->post("/inscricao/{$insc->id}/recurso", [
            'tipo' => 'gabarito', 'questao' => 5, 'texto' => 'A questão 5 tem duas respostas corretas.',
        ])->assertRedirect();

        $this->assertDatabaseHas('recursos', [
            'inscricao_id' => $insc->id, 'tipo' => 'gabarito', 'questao' => 5, 'status' => 'pendente',
        ]);
    }
}
