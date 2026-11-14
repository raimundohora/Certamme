@extends('portal.layout')
@section('title',$concurso->nome)
@section('content')
<a href="{{ route('portal.home') }}" class="text-sm text-slate-500">&larr; Voltar</a>
<h1 class="text-2xl font-bold mt-2 mb-1">{{ $concurso->nome }}</h1>
<div class="text-slate-500 mb-6">Ano {{ $concurso->ano }} · {{ ucfirst($concurso->situacao) }}</div>

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
