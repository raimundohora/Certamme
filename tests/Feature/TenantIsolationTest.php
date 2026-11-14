<?php
namespace Tests\Feature;

use App\Models\Concurso;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_isola_concursos_por_tenant_via_global_scope(): void
    {
        $a = Tenant::create(['nome' => 'Banca A', 'slug' => 'banca-a']);
        $b = Tenant::create(['nome' => 'Banca B', 'slug' => 'banca-b']);

        Tenant::setCurrent($a);
        Concurso::create(['nome' => 'Concurso A1']);

        Tenant::setCurrent($b);
        Concurso::create(['nome' => 'Concurso B1']);

        // Como B: enxergo só o meu, e tenant_id foi preenchido sozinho
        $this->assertSame(1, Concurso::count());
        $this->assertSame('Concurso B1', Concurso::first()->nome);
        $this->assertEquals($b->id, Concurso::first()->tenant_id);

        // Como A: idem
        Tenant::setCurrent($a);
        $this->assertSame(1, Concurso::count());
        $this->assertSame('Concurso A1', Concurso::first()->nome);

        // Sem tenant (admin global): vejo os dois
        Tenant::setCurrent(null);
        $this->assertSame(2, Concurso::count());
    }
}
