<?php
namespace Tests\Feature;

use App\Models\Concurso;
use App\Models\Cronograma;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CronogramaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_cronograma_aparece_no_concurso(): void
    {
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X', 'ano' => 2026, 'situacao' => 'aberto']);
        Cronograma::create(['concurso_id' => $c->id, 'evento' => 'Prova objetiva', 'data' => '2026-12-01']);
        Tenant::setCurrent(null);

        $this->get("/concursos/{$c->id}")->assertOk()->assertSee('Prova objetiva')->assertSee('01/12/2026');
    }
}
