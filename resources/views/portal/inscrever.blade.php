@extends('portal.layout')
@section('title','Inscrição')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-1">Inscrição</h1>
  <p class="text-slate-500 mb-4">{{ $concurso->nome }}</p>
  @if($errors->any())
    <div class="bg-rose-50 text-rose-700 text-sm rounded p-3 mb-3">
      @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
  @endif
  <form method="POST" action="{{ route('portal.inscrever',$concurso) }}" class="space-y-3">
    @csrf
    <div><label class="block text-sm mb-1">Cargo *</label>
      <select name="cargo_id" id="cargo_id" class="w-full border rounded px-3 py-2" required>
        <option value="">Selecione...</option>
        @foreach($concurso->cargos as $cargo)
          <option value="{{ $cargo->id }}" data-taxa="{{ $cargo->taxa_inscricao ?? 0 }}" @selected(old('cargo_id')==$cargo->id)>
            {{ $cargo->nome }} ({{ $cargo->vagas }} vagas) —
            @if($cargo->taxa_inscricao) R$ {{ number_format($cargo->taxa_inscricao,2,',','.') }} @else gratuito @endif
          </option>
        @endforeach
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
    <div id="bloco_metodo"><label class="block text-sm mb-1">Forma de pagamento *</label>
      <select name="metodo" class="w-full border rounded px-3 py-2">
        <option value="pix" @selected(old('metodo')=='pix')>Pix</option>
        <option value="boleto" @selected(old('metodo','boleto')=='boleto')>Boleto bancário</option>
      </select>
    </div>
    <div><label class="block text-sm mb-1">Condição especial (opcional)</label><input name="condicao_especial" value="{{ old('condicao_especial') }}" class="w-full border rounded px-3 py-2" placeholder="Ex.: lactante, sala acessível..."></div>
    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Confirmar inscrição</button>
  </form>
</div>
<script>
  // Esconde a forma de pagamento quando o cargo for gratuito.
  (function(){
    var sel = document.getElementById('cargo_id');
    var bloco = document.getElementById('bloco_metodo');
    function upd(){
      var o = sel.options[sel.selectedIndex];
      var taxa = o ? parseFloat(o.getAttribute('data-taxa')||'0') : 0;
      bloco.style.display = taxa > 0 ? '' : 'none';
    }
    sel.addEventListener('change', upd); upd();
  })();
</script>
@endsection
