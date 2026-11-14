@extends('portal.layout')
@section('title','Comprovante')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-4">Comprovante de inscrição</h1>
  <dl class="divide-y text-sm">
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Nº inscrição</dt><dd class="font-semibold">{{ $inscricao->numero_inscricao }}</dd></div>
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Concurso</dt><dd>{{ $inscricao->concurso->nome }}</dd></div>
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Cargo</dt><dd>{{ $inscricao->cargo?->nome }}</dd></div>
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Candidato</dt><dd>{{ $inscricao->nome }}</dd></div>
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Modalidade</dt><dd>{{ $inscricao->modalidade?->label() }}</dd></div>
    <div class="py-2 flex justify-between"><dt class="text-slate-500">Situação</dt><dd>{{ ucfirst($inscricao->status->value) }}</dd></div>
  </dl>
  @if($pagamento)
    <div class="mt-5 border rounded p-4 bg-slate-50">
      <div class="flex justify-between items-center">
        <div><div class="text-xs text-slate-500">Taxa de inscrição</div><div class="text-lg font-bold">R$ {{ number_format($pagamento->valor,2,',','.') }}</div></div>
        <span class="text-xs px-2 py-1 rounded {{ $pagamento->status->value==='pago' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($pagamento->status->value) }}</span>
      </div>
      @if($pagamento->status->value!=='pago')
        <button class="mt-3 w-full bg-emerald-600 text-white py-2 rounded font-semibold">Pagar com Pix</button>
        <p class="text-xs text-slate-400 mt-2">* pagamento integrado ao gateway (Iugu/Asaas) em configuração.</p>
      @endif
    </div>
  @endif
  <a href="{{ route('portal.painel') }}" class="block text-center mt-4 text-sm text-emerald-700 underline">Minha Área</a>
</div>
@endsection
