@php
    $emitido = $cert->emitido_em;
    $fingerprint = strtoupper(substr($cert->hash, 0, 16));
    $ano = !empty($d['ano']) ? ' (' . $d['ano'] . ')' : '';
    $cargoTxt = !empty($d['cargo']) ? ' para o cargo de ' . $d['cargo'] : '';
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { font-family: DejaVu Serif, serif; }
        body { margin: 0; color: #1f2933; }
        .page { padding: 34px; }
        .frame {
            border: 3px double #1f3a5f;
            padding: 30px 38px 24px;
            min-height: 740px;
            position: relative;
        }
        .seal {
            width: 74px; height: 74px;
            border: 2px solid #1f3a5f;
            border-radius: 50%;
            text-align: center;
            margin: 0 auto 6px;
        }
        .seal .star { font-size: 30px; color: #1f3a5f; line-height: 70px; }
        .orgao { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #1f3a5f; }
        .subheader { text-align: center; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #52606d; margin-top: 2px; }
        .rule { border-top: 1px solid #cbd2d9; margin: 16px 0 22px; }
        .titulo { text-align: center; font-size: 20px; font-weight: bold; letter-spacing: 2px; color: #102a43; margin-bottom: 20px; }
        .corpo { font-size: 13px; line-height: 1.9; text-align: justify; }
        .corpo b { color: #102a43; }
        table.dados { width: 100%; border-collapse: collapse; margin: 22px 0 6px; font-size: 11.5px; }
        table.dados td { padding: 6px 8px; border-bottom: 1px solid #e4e7eb; }
        table.dados td.k { color: #616e7c; width: 36%; }
        table.dados td.v { font-weight: bold; color: #243b53; }
        ul.etapas { font-size: 12px; line-height: 1.7; }
        .assinatura { margin-top: 44px; text-align: center; }
        .assinatura .linha { border-top: 1px solid #52606d; width: 260px; margin: 0 auto; padding-top: 5px; font-size: 11px; color: #52606d; }
        .emissao { text-align: center; font-size: 11px; color: #52606d; margin-top: 26px; }
        .footer {
            position: absolute; left: 38px; right: 38px; bottom: 16px;
            border-top: 1px solid #cbd2d9; padding-top: 8px;
            font-size: 9px; color: #7b8794; font-family: DejaVu Sans, sans-serif;
        }
        .footer .cod { font-weight: bold; color: #243b53; letter-spacing: 1px; }
        .footer table { width: 100%; }
        .footer td.r { text-align: right; }
    </style>
</head>
<body>
<div class="page">
    <div class="frame">
        <div class="seal"><div class="star">&#9733;</div></div>
        <div class="orgao">{{ $d['orgao'] ?? 'Órgão realizador' }}</div>
        <div class="subheader">Concurso Público / Processo Seletivo</div>
        <div class="rule"></div>

        <div class="titulo">{{ $titulo }}</div>

        <div class="corpo">
            @if($tipoValue === 'comprovante_inscricao')
                Comprovamos que <b>{{ $d['nome'] }}</b>, portador(a) do CPF <b>{{ $d['cpf'] }}</b>,
                encontra-se inscrito(a) sob o número <b>{{ $d['numero_inscricao'] }}</b> no
                <b>{{ $d['concurso'] }}{{ $ano }}</b>{{ $cargoTxt }},
                concorrendo pela modalidade <b>{{ $d['modalidade'] ?: 'Ampla concorrência' }}</b>.
                A situação atual da inscrição é <b>{{ $d['status'] }}</b>.

            @elseif($tipoValue === 'declaracao_participacao')
                Declaramos, para os devidos fins, que <b>{{ $d['nome'] }}</b>, portador(a) do CPF
                <b>{{ $d['cpf'] }}</b>, inscrição nº <b>{{ $d['numero_inscricao'] }}</b>,
                participou do <b>{{ $d['concurso'] }}{{ $ano }}</b>{{ $cargoTxt }},
                tendo sido avaliado(a) nas etapas abaixo relacionadas.
                @if(!empty($d['etapas']))
                    <ul class="etapas">
                        @foreach($d['etapas'] as $e)
                            <li>Etapa {{ $e['ordem'] }}@if(!empty($e['tipo'])) &mdash; {{ ucfirst($e['tipo']) }}@endif</li>
                        @endforeach
                    </ul>
                @endif
                Esta declaração destina-se à comprovação de comparecimento ao certame.

            @elseif($tipoValue === 'certificado_classificacao')
                Certificamos que <b>{{ $d['nome'] }}</b>, portador(a) do CPF <b>{{ $d['cpf'] }}</b>,
                inscrição nº <b>{{ $d['numero_inscricao'] }}</b>, foi <b>CLASSIFICADO(A)</b> no
                <b>{{ $d['concurso'] }}{{ $ano }}</b>{{ $cargoTxt }},
                obtendo a <b>{{ $d['classificacao'] }}ª posição</b> na classificação geral.
                @if(!empty($d['classificacao_cota']))
                    Na classificação por {{ $d['modalidade'] }}, obteve a
                    <b>{{ $d['classificacao_cota'] }}ª posição</b>.
                @endif
            @endif
        </div>

        <table class="dados">
            <tr><td class="k">Candidato(a)</td><td class="v">{{ $d['nome'] }}</td></tr>
            <tr><td class="k">CPF</td><td class="v">{{ $d['cpf'] }}</td></tr>
            <tr><td class="k">Nº de inscrição</td><td class="v">{{ $d['numero_inscricao'] }}</td></tr>
            <tr><td class="k">Concurso</td><td class="v">{{ $d['concurso'] }}{{ $ano }}</td></tr>
            @if(!empty($d['cargo']))
                <tr><td class="k">Cargo</td><td class="v">{{ $d['cargo'] }}</td></tr>
            @endif
            @if($tipoValue === 'certificado_classificacao' && !empty($d['classificacao']))
                <tr><td class="k">Classificação geral</td><td class="v">{{ $d['classificacao'] }}º lugar</td></tr>
            @endif
        </table>

        <div class="emissao">
            Documento emitido em {{ $emitido->translatedFormat('d \d\e F \d\e Y, \à\s H:i') }}.
        </div>

        <div class="assinatura">
            <div class="linha">{{ $d['orgao'] ?? 'Órgão realizador' }} &mdash; Comissão Organizadora</div>
        </div>

        <div class="footer">
            <table>
                <tr>
                    <td>
                        Código de autenticação: <span class="cod">{{ $cert->codigo }}</span><br>
                        Confira a validade em: {{ $url }}
                    </td>
                    <td class="r">
                        Fingerprint (SHA-256):<br>{{ $fingerprint }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>
