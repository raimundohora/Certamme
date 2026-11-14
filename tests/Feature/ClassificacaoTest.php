<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\Nota;
use App\Models\Resultado;
use App\Models\Tenant;
use App\Services\ClassificacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_classifica_com_desempate_e_exclui_eliminados(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);

        $ana  = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1001']);
        $caio = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1002']);
        $duda = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1003']);
        $bia  = Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1004']);

        // total 4 acertos 5 ; total 4 acertos 4 (desempate por acertos) ; total 3 ; total 6 mas ELIMINADA
        Nota::create(['inscricao_id' => $ana->id,  'etapa_id' => $e->id, 'disciplina' => 'Geral', 'acertos' => 5, 'nota_bruta' => 4, 'nota_final' => 4, 'eliminado' => false]);
        Nota::create(['inscricao_id' => $caio->id, 'etapa_id' => $e->id, 'disciplina' => 'Geral', 'acertos' => 4, 'nota_bruta' => 4, 'nota_final' => 4, 'eliminado' => false]);
        Nota::create(['inscricao_id' => $duda->id, 'etapa_id' => $e->id, 'disciplina' => 'Geral', 'acertos' => 3, 'nota_bruta' => 3, 'nota_final' => 3, 'eliminado' => false]);
        Nota::create(['inscricao_id' => $bia->id,  'etapa_id' => $e->id, 'disciplina' => 'Geral', 'acertos' => 6, 'nota_bruta' => 6, 'nota_final' => 6, 'eliminado' => true]);

        app(ClassificacaoService::class)->classificar($e);

        $this->assertSame(1, Resultado::where('inscricao_id', $ana->id)->first()->classificacao);
        $this->assertSame(2, Resultado::where('inscricao_id', $caio->id)->first()->classificacao); // desempate por acertos
        $this->assertSame(3, Resultado::where('inscricao_id', $duda->id)->first()->classificacao);

        $biaRes = Resultado::where('inscricao_id', $bia->id)->first();
        $this->assertNull($biaRes->classificacao);      // eliminada: sem classificacao (apesar do maior total)
        $this->assertTrue($biaRes->eliminado);

        $this->assertSame('4.00', Resultado::where('inscricao_id', $ana->id)->first()->total);
    }
}
