@extends('portal.layout')
@section('title','Classificação — '.$concurso->nome)
@section('content')
<div class="max-w-4xl mx-auto space-y-5">
  <a href="{{ route('portal.concurso',$concurso) }}" class="text-sm text-slate-500">&larr; Voltar ao concurso</a>
  <div>
    <h1 class="text-2xl font-bold">Classificação</h1>
    <p class="text-slate-500">{{ $concurso->nome }}@if($concurso->ano) — {{ $concurso->ano }}@endif</p>
  </div>

  @if($etapas->isEmpty())
    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-5">
      A classificação deste concurso <strong>ainda não foi publicada</strong>. Assim que for divulgada oficialmente, ela aparece aqui.
    </div>
  @else
    @if($etapas->count() > 1)
      <div class="flex flex-wrap gap-2">
        @foreach($etapas as $e)
          <a href="{{ route('portal.classificacao',$concurso) }}?etapa={{ $e->ordem }}"
             class="px-3 py-1.5 rounded text-sm font-medium {{ $etapa && $etapa->id===$e->id ? 'bg-emerald-600 text-white' : 'bg-white border text-slate-600 hover:bg-slate-50' }}">
            Etapa {{ $e->ordem }}@if($e->tipo) — {{ ucfirst($e->tipo->value) }}@endif
          </a>
        @endforeach
      </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
      <form method="GET" action="{{ route('portal.classificacao',$concurso) }}" class="flex gap-2 flex-1">
        <input type="hidden" name="etapa" value="{{ $etapa->ordem }}">
        <input name="q" value="{{ $q }}" placeholder="Buscar por nome ou nº de inscrição"
               class="flex-1 border rounded px-3 py-2 text-sm">
        <button class="bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold">Buscar</button>
        @if($q !== '')
          <a href="{{ route('portal.classificacao',$concurso) }}?etapa={{ $etapa->ordem }}" class="px-3 py-2 text-sm text-slate-500 hover:underline">Limpar</a>
        @endif
      </form>
      <a href="{{ route('portal.classificacao.pdf',$concurso) }}?etapa={{ $etapa->ordem }}" target="_blank" rel="noopener"
         class="inline-flex items-center justify-center gap-2 border border-emerald-600 text-emerald-700 hover:bg-emerald-50 px-4 py-2 rounded text-sm font-semibold">⬇️ Baixar PDF</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-5 py-3 border-b bg-slate-50 text-sm text-slate-600">
        Etapa {{ $etapa->ordem }}@if($etapa->tipo) — {{ ucfirst($etapa->tipo->value) }}@endif
        @if($etapa->resultado_publicado_em)<span class="text-xs text-slate-400">· publicada em {{ $etapa->resultado_publicado_em->format('d/m/Y') }}</span>@endif
        @if($q !== '')<span class="text-xs text-slate-400">· filtrando por "{{ $q }}"</span>@endif
      </div>
      @if($linhas->isEmpty())
        <div class="p-5 text-slate-500 text-sm">{{ $q !== '' ? 'Nenhum candidato encontrado para essa busca.' : 'Sem resultados para exibir nesta etapa.' }}</div>
      @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
              <th class="px-4 py-2 w-16">Class.</th>
              <th class="px-4 py-2">Candidato(a)</th>
              <th class="px-4 py-2">Inscrição</th>
              <th class="px-4 py-2">Cargo</th>
              <th class="px-4 py-2">Modalidade</th>
              <th class="px-4 py-2 text-right">Nota</th>
              <th class="px-4 py-2">Situação</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($linhas as $r)
              <tr class="{{ $r->eliminado ? 'text-slate-400' : '' }}">
                <td class="px-4 py-2 font-bold">{{ $r->eliminado ? '—' : ($r->classificacao ?? '—') }}@if(!$r->eliminado && $r->classificacao_cota)<span class="block text-[10px] font-normal text-slate-400">cota {{ $r->classificacao_cota }}º</span>@endif</td>
                <td class="px-4 py-2">{{ $r->inscricao?->nome ?? '—' }}</td>
                <td class="px-4 py-2">{{ $r->inscricao?->numero_inscricao ?? '—' }}</td>
                <td class="px-4 py-2">{{ $r->inscricao?->cargo?->nome ?? '—' }}</td>
                <td class="px-4 py-2">{{ $r->inscricao?->modalidade?->label() ?? '—' }}</td>
                <td class="px-4 py-2 text-right font-medium">{{ $r->total !== null ? number_format((float)$r->total,2,',','.') : '—' }}</td>
                <td class="px-4 py-2">
                  @if($r->eliminado)
                    <span class="text-xs px-2 py-0.5 rounded bg-rose-100 text-rose-700">Eliminado(a)</span>
                  @elseif($r->situacao)
                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">{{ $r->situacaoLabel() }}</span>
                  @else
                    <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600">Classificado(a)</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        </div>
      @endif
    </div>
    <p class="text-xs text-slate-400">Lista oficial publicada pela organizadora. Dúvidas sobre a sua situação: consulte o resultado por CPF ou abra recurso no prazo.</p>
  @endif
</div>
@endsection
