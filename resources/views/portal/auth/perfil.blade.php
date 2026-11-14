@extends('portal.layout')
@section('title','Completar cadastro')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-4">Complete seu cadastro</h1>
  <form method="POST" action="{{ route('portal.perfil.save') }}" class="grid sm:grid-cols-2 gap-3">
    @csrf
    <div><label class="block text-sm mb-1">Data de nascimento *</label><input name="data_nascimento" type="date" value="{{ old('data_nascimento',$candidato->data_nascimento?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Sexo *</label><select name="sexo" class="w-full border rounded px-3 py-2" required><option value="">—</option><option value="Feminino" @selected(old('sexo',$candidato->sexo)=='Feminino')>Feminino</option><option value="Masculino" @selected(old('sexo',$candidato->sexo)=='Masculino')>Masculino</option></select></div>
    <div><label class="block text-sm mb-1">Telefone</label><input name="telefone" value="{{ old('telefone',$candidato->telefone) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Nome social</label><input name="nome_social" value="{{ old('nome_social',$candidato->nome_social) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="sm:col-span-2"><label class="block text-sm mb-1">Nome da mãe *</label><input name="nome_mae" value="{{ old('nome_mae',$candidato->nome_mae) }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">RG</label><input name="rg" value="{{ old('rg',$candidato->rg) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">CEP *</label><input name="cep" value="{{ old('cep',$candidato->cep) }}" class="w-full border rounded px-3 py-2" required></div>
    <div class="sm:col-span-2"><label class="block text-sm mb-1">Logradouro</label><input name="logradouro" value="{{ old('logradouro',$candidato->logradouro) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Número</label><input name="numero" value="{{ old('numero',$candidato->numero) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Bairro</label><input name="bairro" value="{{ old('bairro',$candidato->bairro) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="block text-sm mb-1">Cidade *</label><input name="cidade" value="{{ old('cidade',$candidato->cidade) }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">UF *</label><input name="uf" maxlength="2" value="{{ old('uf',$candidato->uf) }}" class="w-full border rounded px-3 py-2" required></div>
    <div class="sm:col-span-2"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="pcd" value="1" @checked(old('pcd',$candidato->pcd))> Pessoa com deficiência (PcD)</label></div>
    <div class="sm:col-span-2"><label class="block text-sm mb-1">Tipo de deficiência (se PcD)</label><input name="tipo_deficiencia" value="{{ old('tipo_deficiencia',$candidato->tipo_deficiencia) }}" class="w-full border rounded px-3 py-2"></div>
    <div class="sm:col-span-2"><button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Salvar cadastro</button></div>
  </form>
</div>
@endsection
