<?php
namespace Tests\Feature;

use App\Enums\Modalidade;
use App\Enums\StatusInscricao;
use App\Models\Candidato;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class InscricaoRealTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_candidato_e_inscricao_com_modalidade_e_status(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'C']);

        $cand = Candidato::create(['nome' => 'Ana', 'cpf' => '12345678900', 'email' => 'ana@x.com', 'data_nascimento' => '1990-05-10']);
        $insc = Inscricao::create([
            'concurso_id' => $c->id, 'candidato_id' => $cand->id,
            'numero_inscricao' => '1001', 'modalidade' => Modalidade::COTA_PCD, 'status' => StatusInscricao::CONFIRMADA,
        ]);

        $insc->refresh();
        $this->assertSame($t->id, $cand->tenant_id);                      // tenant auto
        $this->assertInstanceOf(Modalidade::class, $insc->modalidade);
        $this->assertSame(Modalidade::COTA_PCD, $insc->modalidade);
        $this->assertSame(StatusInscricao::CONFIRMADA, $insc->status);
        $this->assertSame('Ana', $insc->candidato->nome);
        $this->assertSame(1, $cand->inscricoes()->count());
    }

    public function test_cpf_unico_por_tenant(): void
    {
        $t = Tenant::create(['nome' => 'B', 'slug' => 'b']);
        Tenant::setCurrent($t);
        Candidato::create(['nome' => 'Ana', 'cpf' => '12345678900']);

        $this->expectException(QueryException::class);
        Candidato::create(['nome' => 'Outra', 'cpf' => '12345678900']); // mesmo CPF, mesmo tenant
    }
}
