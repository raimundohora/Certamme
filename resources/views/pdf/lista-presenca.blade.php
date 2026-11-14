<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
  h1 { font-size: 15px; margin: 0 0 2px; }
  .sub { color: #555; margin-bottom: 14px; }
  h2 { font-size: 13px; margin: 18px 0 4px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #333; padding: 5px; text-align: left; }
  th { background: #eee; }
</style>
</head>
<body>
  <h1>Lista de Presença</h1>
  <div class="sub">{{ $etapa->concurso->nome ?? '' }} — Etapa {{ $etapa->ordem }}</div>

  @foreach($grupos as $salaId => $alocacoes)
    @php $sala = $alocacoes->first()->sala; @endphp
    <h2>{{ $sala->local->nome ?? '' }} — {{ $sala->nome }}</h2>
    <table>
      <thead><tr><th style="width:60px">Carteira</th><th style="width:90px">Inscrição</th><th>Nome</th><th style="width:180px">Assinatura</th></tr></thead>
      <tbody>
        @foreach($alocacoes->sortBy('carteira') as $a)
          <tr>
            <td>{{ $a->carteira }}</td>
            <td>{{ $a->inscricao->numero_inscricao }}</td>
            <td>{{ $a->inscricao->nome }}</td>
            <td></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endforeach
</body>
</html>
