<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:#047857">Você foi convocado(a)</h2>
  <p>Olá, {{ $convocacao->inscricao->nome }}.</p>
  <p>Você foi convocado(a) no concurso <b>{{ $convocacao->inscricao->concurso->nome }}</b>
     (inscrição nº {{ $convocacao->inscricao->numero_inscricao }}).</p>
  @if($convocacao->observacao)
    <p style="background:#ecfdf5;padding:8px 12px;border-radius:6px;color:#065f46">{{ $convocacao->observacao }}</p>
  @endif
  <p>Acesse <b>Minha Área</b> no portal para ver os detalhes e acompanhar os próximos passos.</p>
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
