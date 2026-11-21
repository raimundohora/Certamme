@php $status = $recurso->status?->value; @endphp
<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:#047857">Resposta ao seu recurso</h2>
  <p>Olá, {{ $recurso->inscricao->nome }}.</p>
  <p>Seu recurso ({{ $recurso->tipo?->label() }}@if($recurso->questao) · questão {{ $recurso->questao }}@endif) no concurso <b>{{ $recurso->inscricao->concurso->nome }}</b> foi analisado.</p>
  <p>Situação: <b style="color:{{ $status==='deferido' ? '#047857' : ($status==='indeferido' ? '#b91c1c' : '#b45309') }}">{{ ucfirst($status) }}</b></p>
  @if($recurso->resposta)
    <p style="background:#f3f4f6;padding:8px 12px;border-radius:6px"><b>Resposta da banca:</b> {{ $recurso->resposta }}</p>
  @endif
  <p>Acompanhe pelo comprovante da sua inscrição no portal.</p>
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
