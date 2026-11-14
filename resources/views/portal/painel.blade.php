@extends('portal.layout')
@section('title','Minha Área')
@section('content')
<h1 class="text-2xl font-bold mb-1">Olá, {{ $candidato->nome }}</h1>
<p class="text-slate-500 mb-6">Suas inscrições</p>
@unless($candidato->perfilCompleto())
  <div class="mb-4 bg-amber-100 text-amber-800 px-4 py-2 rounded text-sm">Seu cadastro está incompleto. <a href="{{ route('portal.perfil') }}" class="underline font-semibold">Completar agora</a></div>
@endunless
<div class="bg-white rounded-lg shadow divide-y">
@forelse($inscricoes as $i)
  <a href="{{ route('portal.comprovante',$i) }}" class="block px-5 py-3 hover:bg-slate-50 flex justify-between items-center">
    <div><div class="font-medium">{{ $i->concurso->nome }}</div><div class="text-sm text-slate-500">{{ $i->cargo?->nome }} · Nº {{ $i->numero_inscricao }}</div></div>
    <span class="text-xs px-2 py-1 rounded bg-slate-100">{{ ucfirst($i->status->value) }}</span>
  </a>
@empty
  <div class="px-5 py-6 text-center text-slate-500">Você ainda não tem inscrições. <a href="{{ route('portal.home') }}" class="text-emerald-700 underline">Ver concursos</a></div>
@endforelse
</div>
@endsection
