@php $c = \App\Support\ConcursoAtual::model(); @endphp
@if($c)
  <div style="display:flex;align-items:center;gap:.5rem;padding:.25rem .75rem;">
    <span style="font-size:.78rem;color:#92400e;background:#fef3c7;border-radius:9999px;padding:.2rem .7rem;font-weight:600;">📋 {{ $c->nome }} ({{ $c->ano }})</span>
    <a href="/admin/concursos" style="font-size:.75rem;color:#2563eb;text-decoration:underline;">trocar</a>
  </div>
@else
  <div style="padding:.25rem .75rem;font-size:.78rem;color:#6b7280;">Nenhum concurso selecionado — escolha em <a href="/admin/concursos" style="color:#2563eb;text-decoration:underline;font-weight:600;">Concursos</a></div>
@endif
