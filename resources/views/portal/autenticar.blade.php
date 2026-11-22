@extends('portal.layout')
@section('title','Autenticação de documento')
@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold mb-1">Autenticação de documento</h1>
        <p class="text-sm text-slate-500 mb-4">Confira a validade de um documento emitido pelo portal informando o código impresso no rodapé.</p>

        <form method="GET" action="{{ route('portal.autenticar', ['codigo' => '__COD__']) }}"
              onsubmit="event.preventDefault(); var c=this.codigo.value.trim(); if(c) window.location='{{ url('/autenticar') }}/'+encodeURIComponent(c);">
            <div class="flex gap-2">
                <input name="codigo" value="{{ $codigo }}" placeholder="CER-XXXX-XXXX-XXXX"
                       class="flex-1 border rounded px-3 py-2 text-sm uppercase tracking-wider">
                <button class="bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold">Verificar</button>
            </div>
        </form>
    </div>

    @if($cert)
        @php($d = $cert->dados)
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5">
            <div class="flex items-center gap-2 text-emerald-700 font-semibold mb-3">
                <span class="text-lg">✓</span> Documento válido e autêntico
            </div>
            <dl class="divide-y text-sm">
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Tipo</dt><dd class="font-semibold text-right">{{ $cert->tipoEnum()->label() }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Candidato(a)</dt><dd class="text-right">{{ $d['nome'] ?? '—' }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">CPF</dt><dd class="text-right">{{ $d['cpf'] ?? '—' }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Nº de inscrição</dt><dd class="text-right">{{ $d['numero_inscricao'] ?? '—' }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Concurso</dt><dd class="text-right">{{ $d['concurso'] ?? '—' }}@if(!empty($d['ano'])) — {{ $d['ano'] }}@endif</dd></div>
                @if(!empty($d['cargo']))<div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Cargo</dt><dd class="text-right">{{ $d['cargo'] }}</dd></div>@endif
                @if($cert->tipoEnum()->value==='certificado_classificacao' && !empty($d['classificacao']))
                    <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Classificação</dt><dd class="text-right font-semibold">{{ $d['classificacao'] }}º lugar</dd></div>
                @endif
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Emitido em</dt><dd class="text-right">{{ $cert->emitido_em->format('d/m/Y H:i') }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Código</dt><dd class="text-right font-mono text-xs">{{ $cert->codigo }}</dd></div>
                <div class="py-2 flex justify-between gap-4"><dt class="text-slate-500">Fingerprint</dt><dd class="text-right font-mono text-xs">{{ strtoupper(substr($cert->hash,0,16)) }}</dd></div>
            </dl>
            <p class="text-xs text-slate-400 mt-3">A impressão digital (fingerprint) deve coincidir com a do rodapé do PDF. Qualquer divergência indica adulteração.</p>
        </div>
    @elseif($codigo)
        <div class="bg-rose-50 border border-rose-200 rounded-lg p-5 text-rose-700">
            <div class="font-semibold mb-1">✗ Documento não encontrado</div>
            <p class="text-sm">Nenhum documento corresponde ao código <span class="font-mono">{{ $codigo }}</span>. Verifique a digitação ou se o documento é legítimo.</p>
        </div>
    @endif
</div>
@endsection
