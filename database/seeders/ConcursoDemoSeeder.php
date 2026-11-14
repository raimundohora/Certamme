<?php
namespace Database\Seeders;

use App\Enums\Modalidade;
use App\Enums\SituacaoQuestao;
use App\Enums\StatusInscricao;
use App\Enums\TipoEtapa;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Concurso;
use App\Models\Etapa;
use App\Models\GabaritoItem;
use App\Models\Inscricao;
use App\Models\LeituraCartao;
use App\Models\LocalProva;
use App\Models\Prova;
use App\Models\Sala;
use App\Models\Tenant;
use App\Services\CalcularNotaService;
use App\Services\EnsalamentoService;
use App\Services\ClassificacaoService;
use Illuminate\Database\Seeder;

class ConcursoDemoSeeder extends Seeder
{
    public function run(): void
    {
        // re-run limpo: apaga a demo anterior
        Tenant::where('slug', 'instituto-demo')->get()->each->delete();

        $tenant = Tenant::create(['nome' => 'Instituto Demo', 'slug' => 'instituto-demo']);
        Tenant::setCurrent($tenant);

        $concurso = Concurso::create(['nome' => 'Prefeitura Exemplo 2026', 'ano' => 2026, 'situacao' => 'aberto']);
        $cargo = Cargo::create(['concurso_id' => $concurso->id, 'nome' => 'Agente Administrativo', 'vagas' => 5]);

        $etapa = Etapa::create([
            'concurso_id' => $concurso->id, 'ordem' => 1, 'tipo' => TipoEtapa::OBJETIVA,
            'eliminatoria' => true, 'classificatoria' => true, 'nota_minima' => 5,
        ]);
        $prova = Prova::create(['etapa_id' => $etapa->id, 'nome' => 'Objetiva - Caderno 1', 'caderno' => 'Tipo 1', 'qtd_questoes' => 10]);

        // gabarito: 5 Portugues + 5 Matematica; q3 anulada, q8 alterada (A)
        $correct = ['A', 'B', 'C', 'D', 'E', 'A', 'B', 'C', 'D', 'E'];
        for ($n = 1; $n <= 10; $n++) {
            $data = [
                'prova_id' => $prova->id, 'numero' => $n,
                'disciplina' => $n <= 5 ? 'Portugues' : 'Matematica',
                'resposta_correta' => $correct[$n - 1],
            ];
            if ($n === 3) { $data['situacao'] = SituacaoQuestao::ANULADA; }
            if ($n === 8) { $data['situacao'] = SituacaoQuestao::ALTERADA; $data['resposta_nova'] = 'A'; }
            GabaritoItem::create($data);
        }

        $nomes = ['Ana Souza', 'Bruno Lima', 'Carla Dias', 'Diego Rocha', 'Elaine Melo', 'Felipe Alves',
            'Gabriela Nunes', 'Hugo Castro', 'Isabela Pires', 'Joao Vitor', 'Karina Reis', 'Lucas Martins',
            'Mariana Costa', 'Nelson Faria', 'Olivia Ramos', 'Paulo Henrique', 'Queila Sales', 'Rafael Gomes',
            'Sofia Barros', 'Tiago Moura'];
        $mods = [Modalidade::AMPLA, Modalidade::COTA_PCD, Modalidade::COTA_RACIAL];

        mt_srand(42); // deterministico
        foreach ($nomes as $i => $nome) {
            $num = str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);
            $cand = Candidato::create([
                'nome' => $nome,
                'cpf' => str_pad((string) ($i + 1), 11, '0', STR_PAD_LEFT),
                'email' => 'demo' . $num . '@exemplo.com',
                'data_nascimento' => date('Y-m-d', strtotime('1985-01-01 +' . ($i * 5) . ' months')),
            ]);
            $insc = Inscricao::create([
                'concurso_id' => $concurso->id, 'candidato_id' => $cand->id, 'cargo_id' => $cargo->id,
                'numero_inscricao' => $num, 'nome' => $nome,
                'modalidade' => $mods[$i % 3], 'status' => StatusInscricao::CONFIRMADA,
            ]);

            $skill = 0.35 + ((($i * 7) % 20) / 20) * 0.6; // 0.35..0.95 variado
            $resp = [];
            for ($n = 1; $n <= 10; $n++) {
                $acerta = mt_rand(1, 100) <= (int) ($skill * 100);
                $certa = $n === 8 ? 'A' : $correct[$n - 1];
                $resp[(string) $n] = $acerta ? $certa : ($certa === 'A' ? 'B' : 'A');
            }
            LeituraCartao::create(['prova_id' => $prova->id, 'inscricao_id' => $insc->id, 'respostas' => $resp]);
        }

        // Logistica: local + salas + ensalamento (mapa de sala)
        $local = LocalProva::create(['concurso_id' => $concurso->id, 'nome' => 'Escola Municipal Central', 'endereco' => 'Rua Exemplo, 100', 'cidade' => 'Natal']);
        foreach (['Sala 01', 'Sala 02', 'Sala 03'] as $sn) {
            Sala::create(['local_prova_id' => $local->id, 'nome' => $sn, 'capacidade' => 8]);
        }
        app(EnsalamentoService::class)->alocar($etapa);

        app(CalcularNotaService::class)->calcular($prova);
        app(ClassificacaoService::class)->classificar($etapa);

        Tenant::setCurrent(null);
    }
}
