@extends('portal.layout')
@section('title','Comprovante')
@section('content')
<div class="max-w-xl mx-auto space-y-5">
  @if(session('ok'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded p-3">{{ session('ok') }}</div>@endif
  @if(session('info'))<div class="bg-amber-50 text-amber-700 text-sm rounded p-3">{{ session('info') }}</div>@endif
  @foreach($inscricao->convocacoes as $conv)
    <div class="bg-emerald-600 text-white rounded-lg p-4 font-semibold">📣 Você foi CONVOCADO para a Etapa {{ $conv->etapa->ordem }}@if($conv->observacao) — {{ $conv->observacao }}@endif</div>
  @endforeach
  <div class="bg-white rounded-lg shadow p-6">
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
          <div><div class="text-xs text-slate-500">Taxa de inscrição ({{ strtoupper($pagamento->metodo?->value ?? '') }})</div><div class="text-lg font-bold">R$ {{ number_format($pagamento->valor,2,',','.') }}</div></div>
          <span class="text-xs px-2 py-1 rounded {{ $pagamento->status->value==='pago' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($pagamento->status->value) }}</span>
        </div>
        @if($pagamento->status->value==='pago')
          <p class="text-xs text-emerald-600 mt-2">✓ Pagamento confirmado{{ $pagamento->pago_em ? ' em '.$pagamento->pago_em->format('d/m/Y') : '' }}.</p>
        @else
          @if($pagamento->secure_url)
            <a href="{{ $pagamento->secure_url }}" target="_blank" rel="noopener" class="mt-3 block text-center w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold">{{ $pagamento->metodo?->value==='boleto' ? 'Abrir boleto' : ($pagamento->metodo?->value==='pix' ? 'Abrir Pix' : 'Pagar') }}</a>
          @endif
          @if($pagamento->linha_digitavel)
            <div class="mt-3"><div class="text-xs text-slate-500 mb-1">Linha digitável do boleto</div><code class="block text-xs bg-white border rounded p-2 break-all">{{ $pagamento->linha_digitavel }}</code></div>
          @endif
          @if($pagamento->pix_qrcode)
            <div class="mt-3"><div class="text-xs text-slate-500 mb-1">Pix copia e cola</div><code class="block text-xs bg-white border rounded p-2 break-all">{{ $pagamento->pix_qrcode }}</code></div>
          @endif
          @if(empty($pagamento->secure_url) && empty($pagamento->linha_digitavel) && empty($pagamento->pix_qrcode))
            <p class="text-xs text-amber-600 mt-3">Gerando cobrança... atualize a página em instantes.</p>
          @endif
        @endif
      </div>
    @endif
    @if(empty($pagamento) && $inscricao->status->value==='confirmada')
      <div class="mt-5 border rounded p-4 bg-emerald-50 text-emerald-700 text-sm font-medium">Inscrição gratuita — sem taxa a pagar.</div>
    @endif
  </div>

  {{-- Isencao de taxa --}}
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold mb-3">Isenção de taxa</h2>
    @if($inscricao->isencao)
      <div class="text-sm">Pedido enviado — situação:
        <span class="px-2 py-0.5 rounded text-xs {{ $inscricao->isencao->status->value==='deferido' ? 'bg-emerald-100 text-emerald-700' : ($inscricao->isencao->status->value==='indeferido' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($inscricao->isencao->status->value) }}</span>
      </div>
      @if($inscricao->isencao->motivo)<p class="text-xs text-slate-500 mt-1">Motivo: {{ $inscricao->isencao->motivo }}</p>@endif
    @else
      <form method="POST" action="{{ route('portal.isencao.store',$inscricao) }}" class="space-y-2">
        @csrf
        <input name="nis" class="w-full border rounded px-3 py-2 text-sm" placeholder="NIS (CadÚnico)">
        <textarea name="justificativa" class="w-full border rounded px-3 py-2 text-sm" rows="2" placeholder="Justificativa"></textarea>
        <button class="bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold">Pedir isenção</button>
      </form>
    @endif
  </div>

  {{-- Documentos --}}
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold mb-3">Documentos</h2>
    @if($inscricao->documentos->isNotEmpty())
      <ul class="divide-y text-sm mb-3">
        @foreach($inscricao->documentos as $doc)
          <li class="py-2 flex justify-between items-center">
            <a href="{{ $doc->url() ?? '#' }}" target="_blank" class="text-emerald-700 underline">{{ $doc->tipo->label() }}</a>
            <span class="text-xs px-2 py-0.5 rounded bg-slate-100">{{ ucfirst($doc->status->value) }}</span>
          </li>
        @endforeach
      </ul>
    @endif
    <form method="POST" action="{{ route('portal.documento.store',$inscricao) }}" enctype="multipart/form-data" class="space-y-2">
      @csrf
      <select name="tipo" class="w-full border rounded px-3 py-2 text-sm" required>
        <option value="laudo_pcd">Laudo (PcD)</option>
        <option value="titulo">Título</option>
        <option value="isencao">Documento de isenção</option>
        <option value="outros">Outros</option>
      </select>
      <input type="file" name="arquivo" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm" required>
      <button class="bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold">Enviar documento</button>
    </form>
  </div>

  {{-- Recursos --}}
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold mb-3">Recursos</h2>
    @foreach($inscricao->recursos as $rec)
      <div class="border rounded p-3 mb-2 text-sm">
        <div class="flex justify-between"><span class="font-medium">{{ $rec->tipo->label() }}@if($rec->questao) · questão {{ $rec->questao }}@endif</span>
          <span class="px-2 py-0.5 rounded text-xs {{ $rec->status->value==='deferido' ? 'bg-emerald-100 text-emerald-700' : ($rec->status->value==='indeferido' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($rec->status->value) }}</span></div>
        <p class="text-slate-600 mt-1">{{ $rec->texto }}</p>
        @if($rec->resposta)<p class="mt-1 text-slate-800"><b>Resposta da banca:</b> {{ $rec->resposta }}</p>@endif
      </div>
    @endforeach
    <form method="POST" action="{{ route('portal.recurso.store',$inscricao) }}" class="space-y-2 mt-2">
      @csrf
      <select name="tipo" class="w-full border rounded px-3 py-2 text-sm" required>
        <option value="gabarito">Gabarito</option>
        <option value="nota">Nota</option>
        <option value="isencao">Indeferimento de isenção</option>
        <option value="outros">Outros</option>
      </select>
      <input name="questao" type="number" class="w-full border rounded px-3 py-2 text-sm" placeholder="Nº da questão (recurso de gabarito)">
      <textarea name="texto" class="w-full border rounded px-3 py-2 text-sm" rows="3" placeholder="Fundamentação do recurso" required></textarea>
      <button class="bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold">Interpor recurso</button>
    </form>
  </div>

  <a href="{{ route('portal.painel') }}" class="block text-center text-sm text-emerald-700 underline">Minha Área</a>
</div>
@endsection
