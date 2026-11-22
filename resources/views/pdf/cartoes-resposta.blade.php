<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #111; margin: 0; }
        .cartao { page-break-after: always; padding: 12px 14px; }
        .cartao:last-child { page-break-after: auto; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .sub { font-size: 11px; color: #333; margin: 0 0 8px; }
        .ident { border: 1px solid #333; padding: 8px 10px; margin-bottom: 8px; }
        .ident .row { margin: 2px 0; }
        .ident b { font-weight: bold; }
        .ident .big { font-size: 13px; }
        .instr { font-size: 9px; color: #555; margin: 0 0 8px; }
        table.cols { width: 100%; border-collapse: collapse; }
        table.cols > tbody > tr > td { vertical-align: top; padding-right: 10px; width: 33%; }
        table.q { width: 100%; border-collapse: collapse; }
        table.q td { padding: 2px 0; }
        td.qn { width: 26px; font-size: 10px; color: #333; text-align: right; padding-right: 6px; }
        .bolha { display: inline-block; width: 13px; height: 13px; border: 1px solid #333; border-radius: 50%; text-align: center; font-size: 8px; line-height: 13px; color: #777; margin-right: 4px; }
        .foot { margin-top: 10px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
@forelse ($candidatos as $cand)
    <div class="cartao">
        <h1>Cartão-Resposta</h1>
        <p class="sub">{{ $concurso }}{{ $cargo ? ' — ' . $cargo : '' }} · Etapa {{ $etapaOrdem }}</p>

        <div class="ident">
            <div class="row big"><b>{{ $cand['numero'] }}</b> — {{ $cand['nome'] }}</div>
            <div class="row"><b>Local:</b> {{ $cand['local'] }}
                &nbsp;·&nbsp; <b>Sala:</b> {{ $cand['sala'] }}
                &nbsp;·&nbsp; <b>Carteira:</b> {{ $cand['carteira'] }}</div>
            <div class="row"><b>Assinatura:</b> _______________________________________________</div>
        </div>

        @if ($qtdQuestoes > 0)
            <p class="instr">Preencha completamente a bolha da alternativa escolhida, com caneta azul ou preta. Uma única marcação por questão. Rasuras anulam a questão.</p>
            @php
                $questoes  = range(1, $qtdQuestoes);
                $porColuna = (int) ceil($qtdQuestoes / 3);
                $porColuna = max(1, $porColuna);
                $colunas   = array_chunk($questoes, $porColuna);
            @endphp
            <table class="cols">
                <tr>
                    @foreach ($colunas as $coluna)
                        <td>
                            <table class="q">
                                @foreach ($coluna as $n)
                                    <tr>
                                        <td class="qn">{{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            @foreach ($letras as $letra)
                                                <span class="bolha">{{ $letra }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    @endforeach
                </tr>
            </table>
        @else
            <p style="color:#b91c1c">Nº de questões não definido. Cadastre a prova (qtd_questoes) ou informe a quantidade ao gerar os cartões.</p>
        @endif

        <p class="foot">Gerado por Certamme em {{ $geradoEm }}.</p>
    </div>
@empty
    <p style="padding:20px">Nenhum candidato alocado nesta etapa. Rode o ensalamento antes de gerar os cartões-resposta.</p>
@endforelse
</body>
</html>
