@extends('portal.layout')
@section('title','Consultar Resultado')
@section('content')
@php
    $sitLabel = ['aprovado' => 'Aprovado', 'classificavel' => 'Classificável', 'eliminado' => 'Eliminado'];
    $sitClass = [
        'aprovado'      => 'bg-emerald-100 text-emerald-800',
        'classificavel' => 'bg-amber-100 text-amber-800',
        'eliminado'     => 'bg-rose-100 text-rose-700',
    ];
@endphp
<div class="max-w-xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Consultar Resultado</h1>
  <form method="POST" action="{{ route('portal.resultado.consultar') }}" class="bg-white rounded-lg shadow p-5 flex gap-2">
    @csrf
    <input name="cpf" value="{{ $cpf }}" placeholder="Digite seu CPF" class="flex-1 border rounded px-3 py-2" required>
    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 rounded font-semibold">Consultar</button>
  </form>

  @if(! is_null($resultados))
    <div class="mt-6 bg-white rounded-lg shadow divide-y">
    @forelse($resultados as $r)
      <div class="px-5 py-3">
        <div class="font-medium">{{ $r->etapa->concurso->nome ?? 'Concurso' }}</div>
        <div class="text-sm text-slate-500 flex flex-wrap items-center gap-2 mt-1">
          <span>Nota <b>{{ number_format($r->total, 2, ',', '.') }}</b></span>
          <span>· Acertos {{ $r->acertos }}</span>
          @if($r->eliminado)
            <span class="text-rose-600 font-semibold">· Eliminado</span>
          @else
            <span class="text-emerald-700 font-semibold">· {{ $r->classificacao }}º lugar</span>
            @if($r->classificacao_cota)<span>· {{ $r->classificacao_cota }}º na cota</span>@endif
          @endif
          @if($r->situacao)
            <span class="text-xs px-2 py-0.5 rounded {{ $sitClass[$r->situacao] ?? 'bg-slate-100 text-slate-600' }}">{{ $sitLabel[$r->situacao] ?? $r->situacao }}</span>
          @endif
        </div>
      </div>
    @empty
      <div class="px-5 py-6 text-center text-slate-500">Nenhum resultado encontrado para esse CPF.</div>
    @endforelse
    </div>
  @endif
</div>
@endsection
