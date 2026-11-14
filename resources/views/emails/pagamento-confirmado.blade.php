<div style="font-family:sans-serif;max-width:560px;margin:auto;color:#111">
  <h2 style="color:#047857">Pagamento confirmado!</h2>
  <p>Olá, {{ $pagamento->inscricao->nome }}.</p>
  <p>Recebemos o pagamento da taxa da sua inscrição <b>{{ $pagamento->inscricao->numero_inscricao }}</b>
     no concurso <b>{{ $pagamento->inscricao->concurso->nome }}</b>.</p>
  <p>Valor: <b>R$ {{ number_format((float)$pagamento->valor,2,',','.') }}</b> — inscrição confirmada.</p>
  <p style="color:#888;font-size:12px">Este é um e-mail automático, não responda.</p>
</div>
