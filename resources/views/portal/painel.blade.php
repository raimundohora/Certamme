@extends('portal.layout')
@section('title','Minha Área')
@section('content')
@php
    $sitLabel = ['aprovado' => 'Aprovado', 'classificavel' => 'Classificável', 'eliminado' => 'Eliminado'];
    $sitClass = [
        'aprovado'      => 'bg-emerald-100 text-emerald-800',
        'classificavel' => 'bg-amber-100 text-amber-800',
        'eliminado'     => 'bg-rose-100 text-rose-700',
    ];
@endphp
<h1 class="text-2xl font-bold mb-1">Olá, {{ $candidato->nome }}</h1>
<p class="text-slate-500 mb-6">Suas inscrições</p>

@unless($candidato->perfilCompleto())
  <div class="mb-4 bg-amber-100 text-amber-800 px-4 py-2 rounded text-sm">Seu cadastro está incompleto. <a href="{{ route('portal.perfil') }}" class="underline font-semibold">Completar agora</a></div>
@endunless

<div class="space-y-4">
@forelse($inscricoes as $i)
  <div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-3 flex justify-between items-center border-b border-slate-100">
      <div>
        <div class="font-semibold">{{ $i->concurso->nome }}</div>
        <div class="text-sm text-slate-500">{{ $i->cargo?->nome }} · Nº {{ $i->numero_inscricao }} · {{ $i->modalidade?->label() ?? $i->modalidade }}</div>
      </div>
      <span class="text-xs px-2 py-1 rounded bg-slate-100">{{ ucfirst($i->status->value) }}</span>
    </div>

    <div class="px-5 py-3 space-y-3 text-sm">
      {{-- Resultado --}}
      @forelse($i->resultados->sortBy('etapa.ordem') as $r)
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-slate-500">Etapa {{ $r->etapa?->ordem ?? '—' }}:</span>
          <span>Nota <b>{{ number_format($r->total, 2, ',', '.') }}</b></span>
          @if(! $r->eliminado)
            <span class="text-slate-500">· {{ $r->classificacao }}º geral</span>
            @if($r->classificacao_cota)<span class="text-slate-500">· {{ $r->classificacao_cota }}º na cota</span>@endif
          @endif
          @if($r->situacao)
            <span class="text-xs px-2 py-0.5 rounded {{ $sitClass[$r->situacao] ?? 'bg-slate-100 text-slate-600' }}">{{ $sitLabel[$r->situacao] ?? $r->situacao }}</span>
          @endif
        </div>
      @empty
        <div class="text-slate-400">Resultado ainda não divulgado.</div>
      @endforelse

      {{-- Ensalamento --}}
      @foreach($i->alocacoes->sortBy('etapa.ordem') as $a)
        <div class="bg-slate-50 rounded px-3 py-2">
          <span class="text-slate-500">Local de prova (etapa {{ $a->etapa?->ordem ?? '—' }}):</span>
          <b>{{ $a->sala?->local?->nome ?? '—' }}</b>
          @if($a->sala?->local?->endereco) — {{ $a->sala->local->endereco }}@endif
          · Sala <b>{{ $a->sala?->nome ?? '—' }}</b> · Carteira <b>{{ $a->carteira }}</b>
        </div>
      @endforeach

      {{-- Convocação (apenas publicadas) --}}
      @foreach($i->convocacoes as $c)
        <div class="bg-emerald-50 text-emerald-900 rounded px-3 py-2">
          <b>Convocação</b> (etapa {{ $c->etapa?->ordem ?? '—' }}): {{ $c->observacao }}
        </div>
      @endforeach

      <div class="pt-1">
        <a href="{{ route('portal.comprovante', $i) }}" class="text-emerald-700 underline font-medium">Ver comprovante</a>
      </div>
    </div>
  </div>
@empty
  <div class="bg-white rounded-lg shadow px-5 py-6 text-center text-slate-500">Você ainda não tem inscrições. <a href="{{ route('portal.home') }}" class="text-emerald-700 underline">Ver concursos</a></div>
@endforelse
</div>
@endsection
