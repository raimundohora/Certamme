<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #111; margin: 0; }
        .sala { page-break-after: always; padding: 12px 10px; }
        .sala:last-child { page-break-after: auto; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .sub { font-size: 11px; color: #333; margin: 0 0 10px; }
        .box { border: 1px solid #999; padding: 8px 10px; margin-bottom: 10px; }
        .row { margin: 3px 0; }
        .row b { font-weight: bold; }
        .lbl { display: inline-block; min-width: 150px; }
        .linha { border-bottom: 1px solid #999; display: inline-block; min-width: 60px; }
        .sec { font-size: 10px; text-transform: uppercase; color: #444; font-weight: bold; margin: 10px 0 4px; letter-spacing: 1px; }
        .ocorr { border: 1px solid #999; height: 120px; }
        .asss { margin-top: 24px; }
        table.ass { width: 100%; border-collapse: collapse; }
        table.ass td { padding: 18px 8px 2px; text-align: center; font-size: 10px; color: #333; width: 50%; }
        table.ass .ln { border-top: 1px solid #333; padding-top: 3px; }
        .cnt { font-size: 12px; }
        .cnt b { font-size: 14px; }
    </style>
</head>
<body>
@foreach ($salas as $bloco)
    <div class="sala">
        <h1>Ata de Sala</h1>
        <p class="sub">{{ $concurso }}{{ $cargo ? ' — ' . $cargo : '' }} · Etapa {{ $etapaOrdem }}</p>

        <div class="box">
            <div class="row"><span class="lbl"><b>Local:</b></span> {{ $bloco['local'] }}</div>
            @if ($bloco['endereco'])
                <div class="row"><span class="lbl"><b>Endereço:</b></span> {{ $bloco['endereco'] }}</div>
            @endif
            <div class="row"><span class="lbl"><b>Sala:</b></span> {{ $bloco['sala'] }}</div>
            <div class="row"><span class="lbl"><b>Data da aplicação:</b></span> ____/____/______
                &nbsp;&nbsp; <b>Início:</b> ____:____ &nbsp;&nbsp; <b>Término:</b> ____:____</div>
        </div>

        <div class="sec">Frequência</div>
        <div class="box cnt">
            <div class="row"><b>Previstos:</b> {{ $bloco['total'] }}
                &nbsp;·&nbsp; <b>Presentes:</b> ________
                &nbsp;·&nbsp; <b>Ausentes:</b> ________
                &nbsp;·&nbsp; <b>Eliminados:</b> ________</div>
        </div>

        <div class="sec">Material lacrado</div>
        <div class="box">
            <div class="row"><b>Nº do envelope/lacre de provas:</b> <span class="linha">&nbsp;</span>
                &nbsp;&nbsp; <b>Nº do lacre de cartões:</b> <span class="linha">&nbsp;</span></div>
            <div class="row"><b>Horário de abertura dos envelopes:</b> ____:____
                &nbsp;&nbsp; <b>Horário de lacre na devolução:</b> ____:____</div>
        </div>

        <div class="sec">Ocorrências</div>
        <div class="box ocorr">&nbsp;</div>

        <div class="asss">
            <table class="ass">
                <tr>
                    <td class="ln">Aplicador(a) — nome e assinatura</td>
                    <td class="ln">Fiscal — nome e assinatura</td>
                </tr>
                <tr>
                    <td class="ln">Fiscal — nome e assinatura</td>
                    <td class="ln">Coordenador(a) do local</td>
                </tr>
            </table>
        </div>

        <p style="margin-top:14px;font-size:10px;color:#555">Gerado por Certamme em {{ $geradoEm }}.</p>
    </div>
@endforeach
@if (count($salas) === 0)
    <p style="padding:20px">Nenhum candidato alocado nesta etapa. Rode o ensalamento antes de gerar as atas.</p>
@endif
</body>
</html>
