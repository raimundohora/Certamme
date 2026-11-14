<?php
namespace Tests\Feature;

use App\Enums\TipoEtapa;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\Inscricao;
use App\Models\LeituraCartao;
use App\Models\Prova;
use App\Models\Tenant;
use App\Services\ImportarLeituraService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportarLeituraTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    private function cenario(): Prova
    {
        $t = Tenant::create(['nome' => 'Banca', 'slug' => 'banca']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);
        $e = Etapa::create(['concurso_id' => $c->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA]);
        // 1001 e 1002 existem; 9999 NAO
        Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1001', 'nome' => 'Ana']);
        Inscricao::create(['concurso_id' => $c->id, 'numero_inscricao' => '1002', 'nome' => 'Bia']);
        return Prova::create(['etapa_id' => $e->id, 'nome' => 'Objetiva', 'qtd_questoes' => 5]);
    }

    public function test_importa_leitura_e_separa_erros(): void
    {
        $prova = $this->cenario();
        $csv = file_get_contents(base_path('tests/fixtures/leitura_exemplo.csv'));

        $imp = app(ImportarLeituraService::class)->import($prova, $csv);

        $this->assertSame(3, $imp->total);          // 3 linhas de dados (header ignorado)
        $this->assertSame(2, $imp->importados);     // 1001 e 1002
        $this->assertSame(1, $imp->erros);          // 9999 nao existe
        $this->assertStringContainsString('9999', json_encode($imp->erros_detalhe));

        $this->assertSame(2, LeituraCartao::count());
        $leitura = LeituraCartao::whereHas('inscricao', fn ($q) => $q->where('numero_inscricao', '1001'))->first();
        $this->assertSame(['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E'], $leitura->respostas);
    }

    public function test_importacao_idempotente(): void
    {
        $prova = $this->cenario();
        $csv = file_get_contents(base_path('tests/fixtures/leitura_exemplo.csv'));
        $svc = app(ImportarLeituraService::class);

        $svc->import($prova, $csv);
        $svc->import($prova, $csv); // 2a vez

        $this->assertSame(2, LeituraCartao::count()); // nao duplica (1 por candidato/prova)
    }
}
