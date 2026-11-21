@php $def = $isencao->status?->value === 'deferido'; @endphp
<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:{{ $def ? '#047857' : '#b91c1c' }}">Pedido de isenção {{ $def ? 'DEFERIDO' : 'INDEFERIDO' }}</h2>
  <p>Olá, {{ $isencao->inscricao->nome }}.</p>
  <p>Seu pedido de isenção da taxa no concurso <b>{{ $isencao->inscricao->concurso->nome }}</b> foi <b>{{ $def ? 'deferido' : 'indeferido' }}</b>.</p>
  @if($isencao->motivo)
    <p style="background:#f3f4f6;padding:8px 12px;border-radius:6px"><b>Observação da organizadora:</b> {{ $isencao->motivo }}</p>
  @endif
  @if($def)
    <p>Você está isento da taxa de inscrição — nenhum pagamento é necessário.</p>
  @else
    <p>Para efetivar a inscrição, finalize o pagamento da taxa pelo comprovante no portal.</p>
  @endif
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
