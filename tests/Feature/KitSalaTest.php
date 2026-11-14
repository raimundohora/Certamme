<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Alocacao;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\LocalProva;
use App\Models\Sala;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitSalaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_gera_lista_de_presenca_em_pdf(): void
    {
        $user = User::factory()->create();

        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X', 'ano' => 2026, 'situacao' => 'aberto']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        $local = LocalProva::create(['concurso_id' => $c->id, 'nome' => 'Escola Central', 'cidade' => 'Natal']);
        $sala = Sala::create(['local_prova_id' => $local->id, 'nome' => 'Sala 01', 'capacidade' => 5]);
        $insc = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '00001', 'nome' => 'Ana Souza', 'status' => 'confirmada']);
        Alocacao::create(['etapa_id' => $e->id, 'inscricao_id' => $insc->id, 'sala_id' => $sala->id, 'carteira' => 1]);
        Tenant::setCurrent(null);

        $response = $this->actingAs($user)->get("/kit/{$e->id}/lista-presenca");
        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}
