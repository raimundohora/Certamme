@extends('portal.layout')
@section('title','Inscrição')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-1">Inscrição</h1>
  <p class="text-slate-500 mb-4">{{ $concurso->nome }}</p>
  <form method="POST" action="{{ route('portal.inscrever',$concurso) }}" class="space-y-3">
    @csrf
    <div><label class="block text-sm mb-1">Cargo *</label>
      <select name="cargo_id" class="w-full border rounded px-3 py-2" required>
        <option value="">Selecione...</option>
        @foreach($concurso->cargos as $cargo)<option value="{{ $cargo->id }}">{{ $cargo->nome }} ({{ $cargo->vagas }} vagas)</option>@endforeach
      </select>
    </div>
    <div><label class="block text-sm mb-1">Cidade de prova *</label><input name="cidade_prova" value="{{ old('cidade_prova') }}" class="w-full border rounded px-3 py-2" placeholder="Ex.: Natal/RN" required></div>
    <div><label class="block text-sm mb-1">Modalidade *</label>
      <select name="modalidade" class="w-full border rounded px-3 py-2" required>
        <option value="ampla">Ampla concorrência</option>
        <option value="cota_pcd">Cota PcD</option>
        <option value="cota_racial">Cota racial</option>
        <option value="cota_indigena">Cota indígena</option>
      </select>
    </div>
    <div><label class="block text-sm mb-1">Condição especial (opcional)</label><input name="condicao_especial" value="{{ old('condicao_especial') }}" class="w-full border rounded px-3 py-2" placeholder="Ex.: lactante, sala acessível..."></div>
    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Confirmar inscrição</button>
  </form>
</div>
@endsection
