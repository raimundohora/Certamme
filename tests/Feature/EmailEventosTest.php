<?php
namespace Tests\Feature;

use App\Enums\MetodoPagamento;
use App\Mail\InscricaoRealizada;
use App\Mail\PagamentoConfirmado;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Inscricao;
use App\Services\PagamentoService;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailEventosTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::setCurrent(null);
        parent::tearDown();
    }

    public function test_inscricao_dispara_email(): void
    {
        Mail::fake();
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X', 'ano' => 2026, 'situacao' => 'aberto']);
        $cargo = Cargo::create(['concurso_id' => $c->id, 'nome' => 'A', 'vagas' => 1]);
        $cand = Candidato::create([
            'nome' => 'Fulano', 'cpf' => '44444444444', 'email' => 'f@real.com', 'password' => 'segredo1',
            'data_nascimento' => '1990-01-01', 'sexo' => 'Masculino', 'nome_mae' => 'Maria',
            'cep' => '59000000', 'cidade' => 'Natal', 'uf' => 'RN',
        ]);
        Tenant::setCurrent(null);

        $this->actingAs($cand, 'candidato')->post("/concursos/{$c->id}/inscrever", [
            'cargo_id' => $cargo->id, 'cidade_prova' => 'Natal', 'modalidade' => 'ampla',
        ])->assertRedirect();

        Mail::assertSent(InscricaoRealizada::class);
    }

    public function test_pagamento_confirmado_dispara_email(): void
    {
        Mail::fake();
        $t = Tenant::create(['nome' => 'Org', 'slug' => 'org']);
        Tenant::setCurrent($t);
        $c = Concurso::create(['nome' => 'X']);
        $cand = Candidato::create(['nome' => 'F', 'cpf' => '55555555555', 'email' => 'f@real.com', 'password' => 'segredo1']);
        $insc = Inscricao::create(['concurso_id' => $c->id, 'candidato_id' => $cand->id, 'numero_inscricao' => '1', 'nome' => 'F']);
        $svc = app(PagamentoService::class);
        $pag = $svc->criar($insc, 80.00, MetodoPagamento::PIX);
        $pag->update(['external_id' => 'inv_1']);

        $svc->confirmarPorExternalId('inv_1');

        Mail::assertSent(PagamentoConfirmado::class);
    }
}
