<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Certamme — plataforma de gestão de concursos públicos</title>
<meta name="description" content="Do edital à posse: inscrições, pagamento, correção por leitura óptica, resultados, recursos e certificados. Uma plataforma por organizadora, com a sua identidade.">
<script src="https://cdn.tailwindcss.com"></script>
<style>.hero-grad{background:linear-gradient(135deg,#064e3b 0%,#065f46 45%,#047857 100%)}</style>
</head>
<body class="bg-white text-slate-800 antialiased">

<header class="hero-grad text-white">
  <nav class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between">
    <div class="flex items-center gap-2 font-extrabold text-xl tracking-tight">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white text-emerald-700">C</span>
      Certamme
    </div>
    <div class="flex items-center gap-5 text-sm">
      <a href="#recursos" class="hidden sm:inline hover:text-emerald-200">Recursos</a>
      <a href="#como" class="hidden sm:inline hover:text-emerald-200">Como funciona</a>
      <a href="mailto:contato@certamme.com.br" class="bg-white text-emerald-700 font-semibold px-4 py-2 rounded-lg hover:bg-emerald-50">Fale conosco</a>
    </div>
  </nav>

  <div class="max-w-6xl mx-auto px-5 pt-14 pb-20 text-center">
    <span class="inline-block bg-white/10 border border-white/20 rounded-full px-3 py-1 text-xs font-medium mb-5">Para bancas, institutos, fundações e prefeituras</span>
    <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight max-w-3xl mx-auto">Do edital à posse, o seu concurso em uma só plataforma.</h1>
    <p class="mt-5 text-lg text-emerald-100 max-w-2xl mx-auto">Inscrições, cobrança, isenção, logística de provas, correção por leitura óptica, resultados, recursos e certificados — tudo rastreável, do jeito que a Lei Geral dos Concursos exige.</p>
    <div class="mt-8 flex items-center justify-center gap-3">
      <a href="mailto:contato@certamme.com.br" class="bg-white text-emerald-700 font-semibold px-6 py-3 rounded-lg hover:bg-emerald-50">Solicitar demonstração</a>
      <a href="#recursos" class="border border-white/40 px-6 py-3 rounded-lg font-semibold hover:bg-white/10">Ver recursos</a>
    </div>
  </div>
</header>

<section id="recursos" class="max-w-6xl mx-auto px-5 py-16">
  <h2 class="text-3xl font-bold text-center text-slate-900">Tudo que um certame precisa</h2>
  <p class="text-center text-slate-500 mt-2">Sem planilhas soltas e sem sistema caro por etapa.</p>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-10">
    @php
      $f = [
        ['📝','Inscrição online','Portal do candidato, campos configuráveis por concurso, cotas/PcD e heteroidentificação.'],
        ['💳','Pagamento e isenção','Cobrança por boleto/Pix integrada, conciliação automática e pedidos de isenção.'],
        ['🎯','Correção OMR própria','Leitura de cartão-resposta por visão computacional — sem scanner caro. A nota sai sozinha pelo gabarito.'],
        ['🏫','Logística de prova','Ensalamento, mapa de sala, lista de presença e ata — kit de sala pronto em PDF.'],
        ['🏆','Resultados e recursos','Classificação com desempate e cotas, recursos por fase e convocação dos aprovados.'],
        ['📄','Certificados autenticáveis','Comprovante, declaração e certificado em PDF com código de autenticação público.'],
        ['🔌','API REST','Integre os dados do concurso com o site do órgão ou sistemas de transparência.'],
        ['🎨','Marca da sua banca','Portal e painel com o seu logo, as suas cores e o seu domínio (white-label).'],
        ['🔒','Trilha de auditoria','Cada decisão registrada e isolada por organizadora — transparência de ponta a ponta.'],
      ];
    @endphp
    @foreach($f as [$ic,$t,$d])
      <div class="border border-slate-200 rounded-xl p-6 hover:shadow-md transition">
        <div class="text-2xl">{{ $ic }}</div>
        <h3 class="font-semibold text-slate-900 mt-3">{{ $t }}</h3>
        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $d }}</p>
      </div>
    @endforeach
  </div>
</section>

<section id="como" class="bg-slate-50 border-y border-slate-200">
  <div class="max-w-6xl mx-auto px-5 py-16">
    <h2 class="text-3xl font-bold text-center text-slate-900">Como funciona</h2>
    <div class="grid sm:grid-cols-4 gap-6 mt-10 text-center">
      @foreach([
        ['1','Publique o edital','Cadastre cargos, taxas, cronograma e campos de inscrição.'],
        ['2','Receba inscrições','Candidatos se inscrevem e pagam online; tudo concilia sozinho.'],
        ['3','Aplique e corrija','Gere o kit de sala, leia os cartões (OMR) e as notas saem automáticas.'],
        ['4','Publique resultados','Classificação, recursos, convocação e certificados com autenticação.'],
      ] as [$n,$t,$d])
        <div>
          <div class="mx-auto h-10 w-10 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center">{{ $n }}</div>
          <h3 class="font-semibold text-slate-900 mt-3">{{ $t }}</h3>
          <p class="text-sm text-slate-600 mt-1">{{ $d }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="max-w-4xl mx-auto px-5 py-16 text-center">
  <h2 class="text-3xl font-bold text-slate-900">Pronto para o seu próximo concurso?</h2>
  <p class="text-slate-600 mt-3">Fale com a gente e veja a plataforma funcionando com os seus dados.</p>
  <a href="mailto:contato@certamme.com.br" class="inline-block mt-6 bg-emerald-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-emerald-700">Solicitar demonstração</a>
</section>

<footer class="bg-slate-900 text-slate-400 text-sm">
  <div class="max-w-6xl mx-auto px-5 py-8 flex flex-col sm:flex-row items-center justify-between gap-3">
    <div><span class="font-bold text-white">Certamme</span> — plataforma de concursos e processos seletivos</div>
    <div class="text-xs">Um produto <span class="text-slate-300">SistemasDH</span></div>
  </div>
</footer>

</body>
</html>
