<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:#047857">Inscrição realizada!</h2>
  <p>Olá, {{ $inscricao->nome }}.</p>
  <p>Sua inscrição no concurso <b>{{ $inscricao->concurso->nome }}</b> foi registrada com sucesso.</p>
  <table style="border-collapse:collapse;margin:12px 0">
    <tr><td style="padding:4px 8px;color:#555">Nº inscrição</td><td style="padding:4px 8px"><b>{{ $inscricao->numero_inscricao }}</b></td></tr>
    <tr><td style="padding:4px 8px;color:#555">Cargo</td><td style="padding:4px 8px">{{ $inscricao->cargo?->nome }}</td></tr>
    <tr><td style="padding:4px 8px;color:#555">Modalidade</td><td style="padding:4px 8px">{{ $inscricao->modalidade?->label() }}</td></tr>
  </table>
  <p><b>Atenção:</b> finalize o pagamento da taxa para efetivar a inscrição.</p>
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
