@extends('portal.layout')
@section('title','Concursos')
@section('content')
<h1 class="text-2xl font-bold mb-6">Concursos abertos</h1>
<div class="grid gap-4 sm:grid-cols-2">
@forelse($concursos as $c)
  <a href="{{ route('portal.concurso',$c) }}" class="block bg-white rounded-lg shadow p-5 hover:shadow-md transition">
    <div class="text-xs text-slate-500">{{ $c->ano }}</div>
    <div class="font-semibold text-lg">{{ $c->nome }}</div>
    <span class="mt-2 inline-block text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">{{ ucfirst($c->situacao) }}</span>
  </a>
@empty
  <p class="text-slate-500">Nenhum concurso disponível no momento.</p>
@endforelse
</div>
@endsection
