<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtapaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_concurso_tem_etapas_ordenadas_com_tipo_tipado(): void
    {
        $t = Tenant::create(['nome' => 'Banca', 'slug' => 'banca']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'Concurso X']);

        $obj = Etapa::create([
            'concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA,
            'eliminatoria' => true, 'nota_minima' => 50,
        ]);
        Etapa::create(['concurso_id' => $c->id, 'ordem' => 2, 'tipo' => TipoEtapa::REDACAO]);

        $obj->refresh();

        $this->assertSame(2, Etapa::count());
        $this->assertSame($t->id, $obj->tenant_id);                  // tenant preenchido sozinho
        $this->assertInstanceOf(TipoEtapa::class, $obj->tipo);        // enum tipado
        $this->assertSame(TipoEtapa::OBJETIVA, $obj->tipo);
        $this->assertSame('50.00', $obj->nota_minima);               // decimal, nao float
        $this->assertSame('1.00', $obj->peso);                       // default aplicado
        $this->assertTrue($obj->eliminatoria);

        $ordenadas = Etapa::orderBy('ordem')->get()->pluck('tipo')->all();
        $this->assertEquals([TipoEtapa::OBJETIVA, TipoEtapa::REDACAO], $ordenadas);
    }
}
