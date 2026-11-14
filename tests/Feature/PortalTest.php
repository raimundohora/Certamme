<?php
namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_fluxo_candidato_cadastro_perfil_inscricao_resultado(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'Concurso X', 'ano' => 2026, 'situacao' => 'aberto']);
        $cargo = Cargo::create(['concurso_id' => $c->id, 'nome' => 'Agente', 'vagas' => 2]);
        Tenant::setCurrent(null); // o middleware do portal seta a organizadora

        // 1. cadastro -> redireciona pra completar perfil
        $this->post('/cadastro', [
            'nome' => 'Fulano de Tal', 'cpf' => '123.456.789-01', 'email' => 'f@x.com',
            'password' => 'segredo1', 'password_confirmation' => 'segredo1',
        ])->assertRedirect(route('portal.perfil'));
        $this->assertDatabaseHas('candidatos', ['email' => 'f@x.com', 'cpf' => '12345678901']);

        $cand = Candidato::where('email', 'f@x.com')->first();

        // 2. completa cadastro
        $this->actingAs($cand, 'candidato')->post('/perfil', [
            'data_nascimento' => '1990-01-01', 'sexo' => 'Masculino', 'nome_mae' => 'Maria',
            'cep' => '59000-000', 'cidade' => 'Natal', 'uf' => 'RN',
        ])->assertRedirect(route('portal.painel'));
        $this->assertTrue($cand->fresh()->perfilCompleto());

        // 3. inscreve
        $this->actingAs($cand, 'candidato')->post("/concursos/{$c->id}/inscrever", [
            'cargo_id' => $cargo->id, 'cidade_prova' => 'Natal/RN', 'modalidade' => 'ampla',
        ])->assertRedirect();
        $this->assertDatabaseHas('inscricoes', ['concurso_id' => $c->id, 'candidato_id' => $cand->id]);
        $this->assertDatabaseCount('pagamentos', 1);

        // 4. consulta resultado (pagina renderiza)
        $this->post('/resultado', ['cpf' => '12345678901'])->assertOk();

        // 5. paginas publicas
        $this->get('/')->assertOk();
        $this->get("/concursos/{$c->id}")->assertOk();
    }
}
