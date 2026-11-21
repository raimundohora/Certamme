@extends('portal.layout')
@section('title','Completar cadastro')
@section('content')
@php
  $cpfFmt = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->cpf ?? '');
  $tiposPcd = ['Física','Auditiva','Visual','Intelectual','Múltipla','Transtorno do Espectro Autista (TEA)'];
@endphp
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-1">Complete seu cadastro</h1>
  <p class="text-sm text-gray-500 mb-4">Os campos com <span class="text-red-500">*</span> são obrigatórios.</p>
  <form method="POST" action="{{ route('portal.perfil.save') }}" class="grid sm:grid-cols-2 gap-3">
    @csrf

    {{-- Identificação --}}
    <div class="sm:col-span-2 text-xs font-semibold uppercase tracking-wide text-gray-400 border-b pb-1">Identificação</div>

    <div>
      <label class="block text-sm mb-1">CPF</label>
      <input value="{{ $cpfFmt }}" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600" readonly>
      <p class="text-xs text-gray-400 mt-1">Para corrigir o CPF, fale com a organizadora.</p>
    </div>
    <div>
      <label class="block text-sm mb-1">RG <span class="text-gray-400">(opcional)</span></label>
      <input name="rg" value="{{ old('rg',$candidato->rg) }}" class="w-full border rounded px-3 py-2" placeholder="Órgão expedidor/UF se tiver">
    </div>

    <div><label class="block text-sm mb-1">Data de nascimento <span class="text-red-500">*</span></label><input name="data_nascimento" type="date" value="{{ old('data_nascimento',$candidato->data_nascimento?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required></div>
    <div>
      <label class="block text-sm mb-1">Sexo <span class="text-red-500">*</span></label>
      <select name="sexo" class="w-full border rounded px-3 py-2" required>
        <option value="">—</option>
        <option value="Feminino" @selected(old('sexo',$candidato->sexo)=='Feminino')>Feminino</option>
        <option value="Masculino" @selected(old('sexo',$candidato->sexo)=='Masculino')>Masculino</option>
        <option value="Não informado" @selected(old('sexo',$candidato->sexo)=='Não informado')>Prefiro não informar</option>
      </select>
    </div>
    <div><label class="block text-sm mb-1">Telefone</label><input name="telefone" value="{{ old('telefone',$candidato->telefone) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Nome social <span class="text-gray-400">(opcional)</span></label><input name="nome_social" value="{{ old('nome_social',$candidato->nome_social) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="sm:col-span-2"><label class="block text-sm mb-1">Nome da mãe <span class="text-red-500">*</span></label><input name="nome_mae" value="{{ old('nome_mae',$candidato->nome_mae) }}" class="w-full border rounded px-3 py-2" required></div>

    {{-- Endereço --}}
    <div class="sm:col-span-2 mt-2 text-xs font-semibold uppercase tracking-wide text-gray-400 border-b pb-1">Endereço</div>

    <div>
      <label class="block text-sm mb-1">CEP <span class="text-red-500">*</span></label>
      <div class="relative">
        <input id="cep" name="cep" value="{{ old('cep',$candidato->cep) }}" class="w-full border rounded px-3 py-2" placeholder="00000-000" maxlength="9" inputmode="numeric" required>
        <span id="cep-status" class="absolute right-2 top-2.5 text-xs text-gray-400"></span>
      </div>
      <p class="text-xs text-gray-400 mt-1">Digite o CEP que preenchemos o endereço.</p>
    </div>
    <div><label class="block text-sm mb-1">Número</label><input id="numero" name="numero" value="{{ old('numero',$candidato->numero) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="sm:col-span-2"><label class="block text-sm mb-1">Logradouro</label><input id="logradouro" name="logradouro" value="{{ old('logradouro',$candidato->logradouro) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Bairro</label><input id="bairro" name="bairro" value="{{ old('bairro',$candidato->bairro) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="grid grid-cols-3 gap-3">
      <div class="col-span-2"><label class="block text-sm mb-1">Cidade <span class="text-red-500">*</span></label><input id="cidade" name="cidade" value="{{ old('cidade',$candidato->cidade) }}" class="w-full border rounded px-3 py-2" required></div>
      <div><label class="block text-sm mb-1">UF <span class="text-red-500">*</span></label><input id="uf" name="uf" maxlength="2" value="{{ old('uf',$candidato->uf) }}" class="w-full border rounded px-3 py-2 uppercase" required></div>
    </div>

    {{-- Acessibilidade --}}
    <div class="sm:col-span-2 mt-2 text-xs font-semibold uppercase tracking-wide text-gray-400 border-b pb-1">Acessibilidade</div>

    <div class="sm:col-span-2"><label class="flex items-center gap-2 text-sm"><input id="pcd" type="checkbox" name="pcd" value="1" @checked(old('pcd',$candidato->pcd))> Sou pessoa com deficiência (PcD)</label></div>
    <div id="pcd-box" class="sm:col-span-2 {{ old('pcd',$candidato->pcd) ? '' : 'hidden' }}">
      <label class="block text-sm mb-1">Tipo de deficiência</label>
      <select name="tipo_deficiencia" class="w-full border rounded px-3 py-2">
        <option value="">—</option>
        @foreach($tiposPcd as $t)
          <option value="{{ $t }}" @selected(old('tipo_deficiencia',$candidato->tipo_deficiencia)==$t)>{{ $t }}</option>
        @endforeach
      </select>
      <p class="text-xs text-gray-400 mt-1">Classificação conforme Decreto 3.298/1999, Lei 13.146/2015 (LBI) e Lei 12.764/2012 (TEA).</p>
    </div>

    <div class="sm:col-span-2 mt-2"><button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Salvar cadastro</button></div>
  </form>
</div>

<script>
(function(){
  // Máscara + busca de CEP (ViaCEP - base dos Correios)
  var cep = document.getElementById('cep');
  var status = document.getElementById('cep-status');
  function soDigitos(v){ return (v||'').replace(/\D/g,''); }
  function mascara(v){ v = soDigitos(v).slice(0,8); return v.length>5 ? v.slice(0,5)+'-'+v.slice(5) : v; }
  cep.addEventListener('input', function(){ cep.value = mascara(cep.value); });
  cep.addEventListener('blur', buscar);
  function buscar(){
    var v = soDigitos(cep.value);
    if(v.length !== 8){ return; }
    status.textContent = 'buscando…';
    fetch('https://viacep.com.br/ws/'+v+'/json/')
      .then(function(r){ return r.json(); })
      .then(function(d){
        if(d.erro){ status.textContent = 'CEP não encontrado'; return; }
        var set = function(id,val){ var el=document.getElementById(id); if(el && !el.value){ el.value = val||''; } };
        // logradouro/bairro/cidade/uf sempre refrescam a partir do CEP
        document.getElementById('logradouro').value = d.logradouro || '';
        document.getElementById('bairro').value = d.bairro || '';
        document.getElementById('cidade').value = d.localidade || '';
        document.getElementById('uf').value = d.uf || '';
        status.textContent = '✓';
        var num = document.getElementById('numero'); if(num && !num.value){ num.focus(); }
      })
      .catch(function(){ status.textContent = ''; });
  }
  // Mostrar/esconder tipo de deficiência
  var pcd = document.getElementById('pcd');
  var box = document.getElementById('pcd-box');
  pcd.addEventListener('change', function(){ box.classList.toggle('hidden', !pcd.checked); });
  // UF maiúsculo
  var uf = document.getElementById('uf');
  uf.addEventListener('input', function(){ uf.value = uf.value.toUpperCase().replace(/[^A-Z]/g,''); });
})();
</script>
@endsection
