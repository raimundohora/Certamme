@extends('portal.layout')
@section('title',$concurso->nome)
@section('content')
<a href="{{ route('portal.home') }}" class="text-sm text-slate-500">&larr; Voltar</a>
<h1 class="text-2xl font-bold mt-2 mb-1">{{ $concurso->nome }}</h1>
<div class="text-slate-500 mb-6">Ano {{ $concurso->ano }} · {{ ucfirst($concurso->situacao) }}</div>

@if(!empty($temClassificacao))
<a href="{{ route('portal.classificacao',$concurso) }}" class="inline-flex items-center gap-2 mb-6 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded font-medium">🏆 Ver classificação</a>
@endif

@if($concurso->cronogramas->isNotEmpty())
<h2 class="font-semibold mb-2">Cronograma</h2>
<div class="bg-white rounded-lg shadow divide-y mb-6">
  @foreach($concurso->cronogramas as $cr)
    <div class="px-5 py-2 flex justify-between text-sm"><span>{{ $cr->evento }}</span><span class="text-slate-500">{{ $cr->data?->format('d/m/Y') ?? '-' }}</span></div>
  @endforeach
</div>
@endif

@if($concurso->anexos->isNotEmpty())
<h2 class="font-semibold mb-2">Documentos</h2>
<div class="bg-white rounded-lg shadow divide-y mb-6">
  @foreach($concurso->anexos as $anexo)
    <a href="{{ $anexo->url() ?? '#' }}" target="_blank" class="px-5 py-3 flex justify-between items-center hover:bg-slate-50">
      <span>{{ $anexo->titulo }}</span>
      <span class="text-xs px-2 py-0.5 rounded bg-sky-100 text-sky-700">{{ $anexo->grupo->label() }}</span>
    </a>
  @endforeach
</div>
@endif

<h2 class="font-semibold mb-2">Cargos</h2>
<div class="bg-white rounded-lg shadow divide-y mb-6">
@forelse($concurso->cargos as $cargo)
  <div class="px-5 py-3 flex justify-between"><span>{{ $cargo->nome }}</span><span class="text-slate-500 text-sm">{{ $cargo->vagas }} vaga(s)</span></div>
@empty
  <div class="px-5 py-3 text-slate-500">Sem cargos cadastrados.</div>
@endforelse
</div>

@auth('candidato')
  <a href="{{ route('portal.inscrever',$concurso) }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded font-semibold">Inscrever-se</a>
@else
  <a href="{{ route('portal.login') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded font-semibold">Entre para se inscrever</a>
@endauth
@endsection
