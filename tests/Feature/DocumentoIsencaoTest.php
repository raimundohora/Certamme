<?php
namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentoIsencaoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_candidato_envia_documento_e_pede_isencao(): void
    {
        Storage::fake('public');
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X', 'ano' => 2026, 'situacao' => 'aberto']);
        $cargo = Cargo::create(['concurso_id' => $c->id, 'nome' => 'A', 'vagas' => 1]);
        $cand = Candidato::create([
            'nome' => 'Fulano', 'cpf' => '11111111111', 'email' => 'f@x.com', 'password' => 'segredo1',
            'data_nascimento' => '1990-01-01', 'sexo' => 'Masculino', 'nome_mae' => 'Maria',
            'cep' => '59000000', 'cidade' => 'Natal', 'uf' => 'RN',
        ]);
        $insc = Inscricao::create([
            'concurso_id' => $c->id, 'candidato_id' => $cand->id, 'cargo_id' => $cargo->id,
            'numero_inscricao' => '00001', 'nome' => 'Fulano', 'modalidade' => 'ampla', 'status' => 'pendente',
        ]);
        Tenant::setCurrent(null);

        // envia documento
        $this->actingAs($cand, 'candidato')->post("/inscricao/{$insc->id}/documentos", [
            'tipo' => 'laudo_pcd',
            'arquivo' => UploadedFile::fake()->create('laudo.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $this->assertDatabaseHas('documentos', ['inscricao_id' => $insc->id, 'tipo' => 'laudo_pcd', 'status' => 'pendente']);

        // pede isencao
        $this->actingAs($cand, 'candidato')->post("/inscricao/{$insc->id}/isencao", [
            'nis' => '12345678901', 'justificativa' => 'baixa renda',
        ])->assertRedirect();
        $this->assertDatabaseHas('isencoes', ['inscricao_id' => $insc->id, 'status' => 'pendente', 'nis' => '12345678901']);
    }
}
