@php $def = $het->status?->value === 'deferida'; @endphp
<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:{{ $def ? '#047857' : '#b91c1c' }}">Heteroidentificação {{ $def ? 'DEFERIDA' : 'INDEFERIDA' }}</h2>
  <p>Olá, {{ $het->inscricao->nome }}.</p>
  <p>A verificação de heteroidentificação da sua inscrição no concurso
     <b>{{ $het->inscricao->concurso->nome }}</b> (nº {{ $het->inscricao->numero_inscricao }}) foi
     <b>{{ $def ? 'deferida' : 'indeferida' }}</b> pela comissão.</p>
  @if($het->parecer)
    <p style="background:#f3f4f6;padding:8px 12px;border-radius:6px"><b>Parecer da comissão:</b> {{ $het->parecer }}</p>
  @endif
  @if($def)
    <p>Sua inscrição permanece concorrendo pela cota declarada.</p>
  @else
    <p>Conforme o edital, sua inscrição passa a concorrer pela <b>ampla concorrência</b>.
       Você pode interpor recurso no prazo previsto no edital.</p>
  @endif
  <p>Acompanhe os detalhes em <b>Minha Área</b> no portal.</p>
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
