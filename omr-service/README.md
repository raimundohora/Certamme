# Certamme OMR — leitor de cartões-resposta (visão computacional)

Microserviço Python (FastAPI + OpenCV) que **gera** e **lê** os cartões-resposta do
Certamme. Roda em container próprio na rede `certamme_sail`; o Laravel fala com ele
em `http://omr-service:8000` (ver `App\Services\OmrService`).

## Por que é confiável sem scanner caro

O Certamme **gera o próprio cartão**, então o leitor conhece a geometria exata. Um
único módulo (`layout.py`) descreve fiduciais, bloco de identificação e grade de
respostas; `render.py` desenha e `reader.py` lê a partir das MESMAS coordenadas —
gerador e leitor nunca divergem. O nº de inscrição é **pré-impresso** em bolhas, logo
o sistema sabe de quem é o cartão sem depender do candidato preencher o ID.

Pipeline de leitura: acha os 4 fiduciais de canto → homografia para o canvas canônico
→ corrige rotação de 180° pela barra de orientação → binariza (Otsu) → amostra cada
bolha e decide a opção marcada, sinalizando `multipla` / `branco` / `duvida`.

## Endpoints

| Método | Rota       | Entrada                                   | Saída |
|--------|------------|-------------------------------------------|-------|
| GET    | `/health`  | —                                         | `{status:ok}` |
| POST   | `/cartoes` | JSON `{candidatos[], num_questoes, ...}`  | PDF (1 cartão/candidato) |
| POST   | `/read`    | multipart `file` + `num_questoes`, ...    | `{inscricao, respostas{q:op}, flags[]}` |

## Operar

```bash
# build + subir (lê a rede externa certamme_sail do Sail)
docker compose up -d --build
# logs / saúde
docker logs -f omr-service
curl -s http://127.0.0.1:9108/health
# teste de laço fechado (gera -> simula foto -> lê -> confere)
docker run --rm -v "$PWD":/app -w /app certamme-omr:1.0 python test_omr.py
```

## Teste de laço fechado

`test_omr.py` gera cartões com respostas aleatórias, simula foto (perspectiva, ruído,
desfoque, brilho, rotação 180°), lê de volta e exige recuperação exata — além dos casos
de múltipla marcação e questão em branco. É a prova de robustez do leitor.

## Fluxo no admin (Filament → Provas)

1. **Gerar cartões (OMR)** — baixa um PDF com 1 cartão por inscrição confirmada.
2. Imprimir, aplicar a prova, digitalizar/fotografar os cartões.
3. **Ler cartões (OMR)** — enviar as imagens; o sistema casa pelo nº de inscrição,
   grava `LeituraCartao` (origem=omr) e a **nota sai automática** pelo gabarito.
4. Cartões com alerta (`multipla`/`branco`/`duvida`) aparecem no resumo; revisar/ajustar
   em **Leituras de cartão** (editar a leitura recalcula a nota).

Parâmetros por prova (nº de alternativas, dígitos do ID) ficam no modal; o nº de
questões vem de `prova.qtd_questoes`.
