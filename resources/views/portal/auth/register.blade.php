@extends('portal.layout')
@section('title','Cadastro')
@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-1">Criar conta</h1>
  <p class="text-sm text-slate-500 mb-4">Depois você completa o cadastro.</p>
  <form method="POST" action="{{ route('portal.register') }}" class="space-y-3">
    @csrf
    <div><label class="block text-sm mb-1">Nome completo</label><input name="nome" value="{{ old('nome') }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">CPF</label><input name="cpf" value="{{ old('cpf') }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">E-mail</label><input name="email" type="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Senha</label><input name="password" type="password" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Confirmar senha</label><input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" required></div>
    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Criar conta</button>
  </form>
</div>
@endsection
