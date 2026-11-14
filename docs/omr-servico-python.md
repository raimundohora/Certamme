# OMR — serviço de leitura óptica (Python/OpenCV) — PENDENTE DE CARTÕES REAIS

A **configuração de calibração** está pronta (`omr_calibracoes`): identificação (código de barras/QR/marcação/nome do arquivo), threshold (10–80%), formato (retangular/circular), nº de guias/fiduciais, opções (A–E ou V/F), ignorar duplicados.

Falta a **engine de visão computacional** (serviço Python à parte), que **depende de cartões-resposta reais** pra calibrar. Pipeline previsto:
1. Receber a imagem do cartão.
2. Alinhar pelos fiduciais (`qtde_guias`).
3. Identificar o candidato (`identificacao` + `id_chars`).
4. Recortar o grid de bolhas; medir preenchimento; aplicar `threshold`.
5. Gerar `respostas` {numero: letra}.
6. Enviar p/ o Certamme como `leituras_cartao` com `origem=omr` (mesmo fluxo já existente do `ImportarLeituraService`).

> **Ponto-chave:** a saída do OMR é idêntica à importação por CSV/leitora que já funciona e já tem teste. Então a engine pluga no fluxo existente — só precisa dos cartões pra calibrar.
