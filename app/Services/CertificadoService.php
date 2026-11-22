<?php

namespace App\Services;

use App\Enums\TipoCertificado;
use App\Models\Certificado;
use App\Models\Inscricao;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Emite e autentica documentos oficiais de uma inscrição.
 *
 * Regra de elegibilidade (o que o candidato/órgão pode emitir):
 *  - comprovante_inscricao      : sempre que existe a inscrição;
 *  - declaracao_participacao    : quando há ao menos um resultado calculado (participou de etapa avaliada);
 *  - certificado_classificacao  : quando há resultado não eliminado e com classificação definida.
 *
 * O documento é materializado numa linha de `certificados` (uma por inscrição+tipo),
 * com um código público e um hash do snapshot. Re-emitir só gera nova versão
 * (e nova data) se os dados mudaram — o código permanece estável.
 */
class CertificadoService
{
    /** @return list<TipoCertificado> */
    public function disponiveis(Inscricao $inscricao): array
    {
        $tipos = [TipoCertificado::COMPROVANTE_INSCRICAO];

        if ($inscricao->resultados()->exists()) {
            $tipos[] = TipoCertificado::DECLARACAO_PARTICIPACAO;
        }

        $classificado = $inscricao->resultados()
            ->where('eliminado', false)
            ->whereNotNull('classificacao')
            ->exists();
        if ($classificado) {
            $tipos[] = TipoCertificado::CERTIFICADO_CLASSIFICACAO;
        }

        return $tipos;
    }

    public function podeEmitir(Inscricao $inscricao, TipoCertificado $tipo): bool
    {
        return in_array($tipo, $this->disponiveis($inscricao), true);
    }

    public function emitir(Inscricao $inscricao, TipoCertificado $tipo): Certificado
    {
        if (! $this->podeEmitir($inscricao, $tipo)) {
            throw new \RuntimeException('Documento indisponível para esta inscrição.');
        }

        $dados    = $this->snapshot($inscricao, $tipo);
        $novoHash = hash('sha256', json_encode($dados, JSON_UNESCAPED_UNICODE));

        $cert = Certificado::firstOrNew([
            'inscricao_id' => $inscricao->id,
            'tipo'         => $tipo->value,
        ]);

        if (! $cert->exists || $cert->hash !== $novoHash) {
            if (! $cert->exists) {
                $cert->codigo = $this->gerarCodigo();
            }
            $cert->dados      = $dados;
            $cert->hash       = $novoHash;
            $cert->emitido_em = now();
            $cert->save();
        }

        return $cert;
    }

    /** Instância de PDF pronta para stream/download. */
    public function pdf(Certificado $cert)
    {
        return Pdf::loadView('pdf.certificado', [
            'cert'      => $cert,
            'tipoValue' => $cert->tipo,
            'titulo'    => $cert->tipoEnum()->titulo(),
            'd'         => $cert->dados,
            'url'       => url('/autenticar/' . $cert->codigo),
        ])->setPaper('a4');
    }

    public function verificar(string $codigo): ?Certificado
    {
        $codigo = mb_strtoupper(trim($codigo));

        return Certificado::query()->where('codigo', $codigo)->first();
    }

    /** Snapshot imutável dos dados exibidos no documento. */
    private function snapshot(Inscricao $inscricao, TipoCertificado $tipo): array
    {
        $inscricao->loadMissing(['concurso', 'cargo', 'candidato']);

        $dados = [
            'orgao'            => Tenant::current()?->nome ?? 'Órgão realizador',
            'nome'             => $inscricao->nome ?: $inscricao->candidato?->nome,
            'cpf'              => $this->mascararCpf($inscricao->candidato?->cpf),
            'concurso'         => $inscricao->concurso?->nome,
            'ano'              => $inscricao->concurso?->ano,
            'cargo'            => $inscricao->cargo?->nome,
            'numero_inscricao' => $inscricao->numero_inscricao,
            'modalidade'       => $inscricao->modalidade?->label(),
            'status'           => ucfirst($inscricao->status?->value ?? ''),
        ];

        if (in_array($tipo, [TipoCertificado::DECLARACAO_PARTICIPACAO, TipoCertificado::CERTIFICADO_CLASSIFICACAO], true)) {
            $resultados = $inscricao->resultados()->with('etapa')->get();
            $dados['etapas'] = $resultados
                ->sortBy(fn ($r) => $r->etapa?->ordem)
                ->map(fn ($r) => [
                    'ordem'     => $r->etapa?->ordem,
                    'tipo'      => $r->etapa?->tipo?->value,
                    'eliminado' => (bool) $r->eliminado,
                ])->values()->all();
        }

        if ($tipo === TipoCertificado::CERTIFICADO_CLASSIFICACAO) {
            $melhor = $inscricao->resultados()
                ->where('eliminado', false)
                ->whereNotNull('classificacao')
                ->with('etapa')
                ->get()
                ->sortByDesc(fn ($r) => $r->etapa?->ordem)
                ->first();

            $dados['classificacao']       = $melhor?->classificacao;
            $dados['classificacao_cota']  = $melhor?->classificacao_cota;
            $dados['etapa_ordem']         = $melhor?->etapa?->ordem;
        }

        return $dados;
    }

    /** Código público legível, sem caracteres ambíguos. Ex.: CER-A4KP-7QX3-RN29 */
    private function gerarCodigo(): string
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $tam      = strlen($alfabeto);

        do {
            $raw = '';
            for ($i = 0; $i < 12; $i++) {
                $raw .= $alfabeto[random_int(0, $tam - 1)];
            }
            $codigo = 'CER-' . substr($raw, 0, 4) . '-' . substr($raw, 4, 4) . '-' . substr($raw, 8, 4);
        } while (Certificado::withoutGlobalScopes()->where('codigo', $codigo)->exists());

        return $codigo;
    }

    private function mascararCpf(?string $cpf): ?string
    {
        if (! $cpf) {
            return null;
        }
        $d = preg_replace('/\D/', '', $cpf);
        if (strlen($d) !== 11) {
            return $cpf;
        }

        // ***.XXX.XXX-** — mantém os 6 dígitos centrais (padrão LGPD de editais)
        return '***.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-**';
    }
}
