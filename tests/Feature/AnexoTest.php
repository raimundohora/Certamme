<?php
namespace Tests\Feature;

use App\Models\Anexo;
use App\Models\Concurso;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnexoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_anexo_publicado_aparece_no_concurso_e_rascunho_nao(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'Concurso X', 'ano' => 2026, 'situacao' => 'aberto']);
        Anexo::create(['concurso_id' => $c->id, 'titulo' => 'Edital 01/2026', 'grupo' => 'edital', 'publicado' => true]);
        Anexo::create(['concurso_id' => $c->id, 'titulo' => 'Rascunho interno', 'grupo' => 'outros', 'publicado' => false]);
        Tenant::setCurrent(null);

        $this->get("/concursos/{$c->id}")
            ->assertOk()
            ->assertSee('Edital 01/2026')
            ->assertDontSee('Rascunho interno');
    }
}
