<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #111; margin: 0; }
        .sala { page-break-after: always; padding: 10px 6px; }
        .sala:last-child { page-break-after: auto; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .sub { font-size: 11px; color: #333; margin: 0 0 8px; }
        .meta { font-size: 11px; margin: 0 0 10px; }
        .meta b { font-weight: bold; }
        .frente { text-align: center; background: #222; color: #fff; padding: 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
        table.grade { width: 100%; border-collapse: separate; border-spacing: 6px; }
        table.grade td { border: 1px solid #666; border-radius: 4px; padding: 6px 4px; width: 20%; height: 44px; vertical-align: top; }
        .cart { font-size: 9px; color: #666; }
        .num { font-weight: bold; font-size: 12px; }
        .nome { font-size: 9px; color: #222; }
        .vazio { border: 1px dashed #bbb !important; }
        .foot { margin-top: 14px; font-size: 10px; color: #555; }
    </style>
</head>
<body>
@foreach ($salas as $bloco)
    <div class="sala">
        <h1>Mapa de Sala</h1>
        <p class="sub">{{ $concurso }}{{ $cargo ? ' — ' . $cargo : '' }} · Etapa {{ $etapaOrdem }}</p>
        <p class="meta">
            <b>Local:</b> {{ $bloco['local'] }}
            &nbsp;·&nbsp; <b>Sala:</b> {{ $bloco['sala'] }}
            &nbsp;·&nbsp; <b>Candidatos:</b> {{ $bloco['total'] }}
            @if ($bloco['capacidade'] > 0)
                &nbsp;·&nbsp; <b>Capacidade:</b> {{ $bloco['capacidade'] }}
            @endif
        </p>
        <div class="frente">◆ Frente da sala (mesa do aplicador) ◆</div>
        <table class="grade">
            @foreach ($bloco['fileiras'] as $fileira)
                <tr>
                    @foreach ($fileira as $assento)
                        <td>
                            <div class="cart">Carteira {{ $assento['carteira'] }}</div>
                            <div class="num">{{ $assento['numero'] }}</div>
                            <div class="nome">{{ \Illuminate\Support\Str::limit($assento['nome'], 34) }}</div>
                        </td>
                    @endforeach
                    @for ($i = count($fileira); $i < $bloco['colunas']; $i++)
                        <td class="vazio">&nbsp;</td>
                    @endfor
                </tr>
            @endforeach
        </table>
        <p class="foot">Gerado por Certamme em {{ $geradoEm }}. Disposição sugerida — ajuste conforme a sala física.</p>
    </div>
@endforeach
@if (count($salas) === 0)
    <p style="padding:20px">Nenhum candidato alocado nesta etapa. Rode o ensalamento antes de gerar o mapa de sala.</p>
@endif
</body>
</html>
