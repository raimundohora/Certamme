<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Concursos') — Certamme</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
<header class="bg-slate-900 text-white">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('portal.home') }}" class="font-bold text-lg tracking-tight">Certamme</a>
    <nav class="flex items-center gap-4 text-sm">
      <a href="{{ route('portal.home') }}" class="hover:underline">Início</a>
      <a href="{{ route('portal.resultado') }}" class="hover:underline">Consultar Resultado</a>
      @auth('candidato')
        <a href="{{ route('portal.painel') }}" class="hover:underline">Minha Área</a>
        <form method="POST" action="{{ route('portal.logout') }}">@csrf<button class="hover:underline">Sair</button></form>
      @else
        <a href="{{ route('portal.login') }}" class="hover:underline">Entrar</a>
        <a href="{{ route('portal.register') }}" class="bg-emerald-500 hover:bg-emerald-600 px-3 py-1 rounded font-medium">Cadastrar</a>
      @endauth
    </nav>
  </div>
</header>
<main class="flex-1 max-w-5xl mx-auto w-full px-4 py-8">
  @if(session('ok'))<div class="mb-4 bg-emerald-100 text-emerald-800 px-4 py-2 rounded">{{ session('ok') }}</div>@endif
  @if(session('info'))<div class="mb-4 bg-sky-100 text-sky-800 px-4 py-2 rounded">{{ session('info') }}</div>@endif
  @if($errors->any())<div class="mb-4 bg-rose-100 text-rose-800 px-4 py-2 rounded"><ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  @yield('content')
</main>
<footer class="bg-slate-900 text-slate-400 text-xs text-center py-4">Certamme — plataforma de concursos</footer>
</body>
</html>
