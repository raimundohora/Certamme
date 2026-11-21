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
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; vertical-align: middle; }
        th { background: #eee; font-size: 10px; text-transform: uppercase; }
        td.c { text-align: center; width: 48px; }
        td.n { width: 70px; text-align: center; }
        td.sig { width: 180px; }
        .foot { margin-top: 14px; font-size: 10px; color: #555; }
    </style>
</head>
<body>
@foreach ($salas as $bloco)
    <div class="sala">
        <h1>Lista de Presença</h1>
        <p class="sub">{{ $concurso }}{{ $cargo ? ' — '.$cargo : '' }} · Etapa {{ $etapaOrdem }}</p>
        <p class="meta">
            <b>Local:</b> {{ $bloco['local'] }}
            &nbsp;·&nbsp; <b>Sala:</b> {{ $bloco['sala'] }}
            &nbsp;·&nbsp; <b>Candidatos:</b> {{ count($bloco['linhas']) }}
            &nbsp;·&nbsp; <b>Data:</b> ____/____/______
        </p>
        <table>
            <thead>
                <tr>
                    <th class="c">Cart.</th>
                    <th>Nº inscrição</th>
                    <th>Candidato</th>
                    <th>Assinatura</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bloco['linhas'] as $l)
                    <tr>
                        <td class="c">{{ $l['carteira'] }}</td>
                        <td class="n">{{ $l['numero'] }}</td>
                        <td>{{ $l['nome'] }}</td>
                        <td class="sig"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="foot">Gerado por Certamme em {{ $geradoEm }}. Aplicador: _____________________________  Assinatura: _____________________</p>
    </div>
@endforeach
@if(count($salas) === 0)
    <p style="padding:20px">Nenhum candidato alocado nesta etapa. Rode o ensalamento antes de gerar a lista.</p>
@endif
</body>
</html>
