<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<style>
  * { font-family: DejaVu Sans, sans-serif; }
  body { margin: 0; color: #1f2933; font-size: 11px; }
  .page { padding: 28px 34px; }
  .orgao { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; color: #1f3a5f; }
  .sub { text-align: center; font-size: 10px; color: #52606d; margin-top: 2px; }
  h1 { text-align: center; font-size: 15px; color: #102a43; margin: 14px 0 2px; }
  .etapa { text-align: center; font-size: 10px; color: #616e7c; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1f3a5f; color: #fff; text-align: left; padding: 5px 6px; font-size: 10px; }
  td { padding: 4px 6px; border-bottom: 1px solid #e4e7eb; font-size: 10px; }
  td.c { text-align: center; } td.r { text-align: right; }
  tr.elim td { color: #9aa5b1; }
  .pos { font-weight: bold; }
  .foot { margin-top: 14px; font-size: 9px; color: #7b8794; text-align: center; }
</style>
</head>
<body>
<div class="page">
  <div class="orgao">{{ $orgao }}</div>
  <div class="sub">{{ $concurso->nome }}@if($concurso->ano) — {{ $concurso->ano }}@endif</div>
  <h1>CLASSIFICAÇÃO</h1>
  <div class="etapa">Etapa {{ $etapa->ordem }}@if($etapa->tipo) — {{ ucfirst($etapa->tipo->value) }}@endif
    @if($etapa->resultado_publicado_em)· publicada em {{ $etapa->resultado_publicado_em->format('d/m/Y') }}@endif</div>

  <table>
    <thead>
      <tr>
        <th style="width:42px">Class.</th>
        <th>Candidato(a)</th>
        <th style="width:80px">Inscrição</th>
        <th>Cargo</th>
        <th>Modalidade</th>
        <th style="width:52px">Nota</th>
        <th style="width:70px">Situação</th>
      </tr>
    </thead>
    <tbody>
      @forelse($linhas as $r)
        <tr class="{{ $r->eliminado ? 'elim' : '' }}">
          <td class="c pos">{{ $r->eliminado ? '—' : ($r->classificacao ?? '—') }}@if(!$r->eliminado && $r->classificacao_cota) <span style="font-weight:normal">(cota {{ $r->classificacao_cota }}º)</span>@endif</td>
          <td>{{ $r->inscricao?->nome ?? '—' }}</td>
          <td>{{ $r->inscricao?->numero_inscricao ?? '—' }}</td>
          <td>{{ $r->inscricao?->cargo?->nome ?? '—' }}</td>
          <td>{{ $r->inscricao?->modalidade?->label() ?? '—' }}</td>
          <td class="r">{{ $r->total !== null ? number_format((float)$r->total,2,',','.') : '—' }}</td>
          <td>{{ $r->eliminado ? 'Eliminado(a)' : ($r->situacao ? ucfirst($r->situacao) : 'Classificado(a)') }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="c">Sem resultados.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="foot">Documento gerado em {{ now()->format('d/m/Y H:i') }} · {{ $orgao }} — lista oficial de classificação.</div>
</div>
</body>
</html>
