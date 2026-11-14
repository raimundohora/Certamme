@extends('portal.layout')
@section('title','Entrar')
@section('content')
<div class="max-w-sm mx-auto bg-white rounded-lg shadow p-6">
  <h1 class="text-xl font-bold mb-4">Entrar</h1>
  <form method="POST" action="{{ route('portal.login') }}" class="space-y-3">
    @csrf
    <div><label class="block text-sm mb-1">E-mail</label><input name="email" type="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Senha</label><input name="password" type="password" class="w-full border rounded px-3 py-2" required></div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Lembrar de mim</label>
    <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">Entrar</button>
  </form>
  <p class="text-sm text-center mt-4 text-slate-500">Não tem conta? <a href="{{ route('portal.register') }}" class="text-emerald-700 underline">Cadastre-se</a></p>
</div>
@endsection
