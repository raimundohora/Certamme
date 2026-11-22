<?php

namespace App\Services;

use App\Enums\OrigemLeitura;
use App\Enums\StatusInscricao;
use App\Models\Importacao;
use App\Models\Inscricao;
use App\Models\LeituraCartao;
use App\Models\Prova;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Ponte entre o Laravel e o microserviço de OMR (visão computacional) que roda
 * em container próprio na rede certamme_sail (http://omr-service:8000).
 *
 * - gerarCartoesPdf(): PDF com 1 cartão por candidato (mesma geometria que o leitor).
 * - processarUploads(): lê imagens de cartões e grava LeituraCartao (origem=omr).
 *   A partir daí o pipeline existente (observer -> CalcularNotaService) gera as Notas.
 */
class OmrService
{
    private string $base;

    public function __construct()
    {
        $this->base = rtrim((string) config('services.omr.url', 'http://omr-service:8000'), '/');
    }

    public function disponivel(): bool
    {
        try {
            return Http::timeout(4)->get($this->base.'/health')->json('status') === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }

    /** Candidatos (inscrições confirmadas) elegíveis a cartão para esta prova. */
    public function candidatosDaProva(Prova $prova): array
    {
        $prova->loadMissing('etapa');
        $etapa = $prova->etapa;

        $q = Inscricao::where('concurso_id', $etapa->concurso_id)
            ->where('status', StatusInscricao::CONFIRMADA);
        if ($etapa->cargo_id) {
            $q->where('cargo_id', $etapa->cargo_id);
        }

        $elegiveis = [];
        $ignorados = [];
        foreach ($q->orderBy('numero_inscricao')->get() as $insc) {
            if (ctype_digit((string) $insc->numero_inscricao)) {
                $elegiveis[] = ['inscricao' => (string) $insc->numero_inscricao, 'nome' => $insc->nome];
            } else {
                $ignorados[] = $insc->numero_inscricao;
            }
        }

        return ['candidatos' => $elegiveis, 'ignorados' => $ignorados];
    }

    /** Gera o PDF de cartões (bytes) para impressão. */
    public function gerarCartoesPdf(Prova $prova, int $numOpcoes = 5, int $idDigits = 6): array
    {
        $prova->loadMissing('etapa.concurso', 'etapa.cargo');
        $dados = $this->candidatosDaProva($prova);

        if (empty($dados['candidatos'])) {
            return ['ok' => false, 'erro' => 'Nenhuma inscrição confirmada com número numérico.', 'ignorados' => $dados['ignorados']];
        }

        $concurso = $prova->etapa->concurso;
        $cargo = $prova->etapa->cargo;
        $subtitulo = trim(($concurso->nome ?? '').($cargo ? '  •  '.$cargo->nome : ''));

        $resp = Http::timeout(120)->post($this->base.'/cartoes', [
            'candidatos'   => $dados['candidatos'],
            'num_questoes' => (int) ($prova->qtd_questoes ?: 50),
            'num_opcoes'   => $numOpcoes,
            'id_digits'    => $idDigits,
            'titulo'       => 'CARTÃO-RESPOSTA',
            'subtitulo'    => $subtitulo,
        ]);

        if (! $resp->successful()) {
            return ['ok' => false, 'erro' => 'Serviço OMR falhou ('.$resp->status().').'];
        }

        return [
            'ok' => true,
            'pdf' => $resp->body(),
            'qtd' => count($dados['candidatos']),
            'ignorados' => $dados['ignorados'],
        ];
    }

    /** Lê uma imagem de cartão; devolve o dict do leitor (inscricao, respostas, flags). */
    public function lerImagem(Prova $prova, string $conteudo, string $filename, int $numOpcoes = 5, int $idDigits = 6): array
    {
        $resp = Http::timeout(60)
            ->attach('file', $conteudo, $filename)
            ->post($this->base.'/read', [
                'num_questoes' => (int) ($prova->qtd_questoes ?: 50),
                'num_opcoes'   => $numOpcoes,
                'id_digits'    => $idDigits,
            ]);

        if (! $resp->successful()) {
            return ['ok' => false, 'erro' => $resp->json('erro') ?? ('HTTP '.$resp->status()), 'arquivo' => $filename];
        }

        return $resp->json();
    }

    /**
     * Processa uma lista de imagens [[conteudo, filename], ...] de cartões desta prova.
     * Para cada uma: lê, casa a inscrição, grava LeituraCartao (origem=omr). Idempotente.
     */
    public function processarUploads(Prova $prova, array $imagens, int $numOpcoes = 5, int $idDigits = 6): Importacao
    {
        $prova->loadMissing('etapa');
        $concursoId = $prova->etapa->concurso_id;

        $importacao = Importacao::create(['prova_id' => $prova->id, 'arquivo' => 'omr']);

        $total = 0; $importados = 0; $erros = [];

        foreach ($imagens as [$conteudo, $filename]) {
            $total++;
            $out = $this->lerImagem($prova, $conteudo, $filename, $numOpcoes, $idDigits);

            if (! ($out['ok'] ?? false) && empty($out['inscricao'])) {
                $erros[] = ['arquivo' => $filename, 'motivo' => $out['erro'] ?? 'leitura falhou'];
                continue;
            }

            $inscricao = $this->casarInscricao($concursoId, (string) ($out['inscricao'] ?? ''));
            if (! $inscricao) {
                $erros[] = ['arquivo' => $filename, 'motivo' => "inscrição {$out['inscricao']} não encontrada"];
                continue;
            }

            LeituraCartao::updateOrCreate(
                ['prova_id' => $prova->id, 'inscricao_id' => $inscricao->id],
                ['respostas' => $out['respostas'] ?? [], 'origem' => OrigemLeitura::OMR, 'importacao_id' => $importacao->id],
            );
            $importados++;

            $flags = array_values(array_filter($out['flags'] ?? [], fn ($f) => str_starts_with($f, 'q') || str_starts_with($f, 'id')));
            if (! empty($flags)) {
                $erros[] = ['arquivo' => $filename, 'inscricao' => $inscricao->numero_inscricao,
                            'motivo' => 'revisar: '.implode(', ', array_slice($flags, 0, 12)), 'aviso' => true];
            }
        }

        $importacao->update([
            'total' => $total,
            'importados' => $importados,
            'erros' => count(array_filter($erros, fn ($e) => empty($e['aviso']))),
            'erros_detalhe' => $erros,
        ]);

        return $importacao->refresh();
    }

    private function casarInscricao(int $concursoId, string $inscricao): ?Inscricao
    {
        if ($inscricao === '') {
            return null;
        }
        $m = Inscricao::where('concurso_id', $concursoId)->where('numero_inscricao', $inscricao)->first();
        if ($m) {
            return $m;
        }
        // tolera zero-padding divergente entre o cartão e o cadastro (ex.: "0001" x "1").
        // ltrim(col,'0') é compatível com PostgreSQL; comparação puramente textual (sem cast).
        if (ctype_digit($inscricao)) {
            $norm = ltrim($inscricao, '0');
            $norm = $norm === '' ? '0' : $norm;
            return Inscricao::where('concurso_id', $concursoId)
                ->whereRaw("ltrim(numero_inscricao, '0') = ?", [$norm])
                ->first();
        }
        return null;
    }
}
