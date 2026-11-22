"""
Desenha o cartão-resposta canônico (mesma geometria que o leitor usa).

- `render_card(spec, inscricao, respostas=None, scale=...)` devolve uma imagem PIL.
- O nº de inscrição é PRÉ-IMPRESSO (bolhas de ID preenchidas pelo gerador), então
  o leitor sempre sabe de quem é o cartão sem depender do candidato preencher o ID.
- `respostas` (dict {q: "A"}) só é usado em testes / amostras para simular um cartão
  já marcado pelo candidato.
"""
from __future__ import annotations

from PIL import Image, ImageDraw, ImageFont

import layout as L


def _font(size: int):
    for name in ("DejaVuSans.ttf", "DejaVuSans-Bold.ttf"):
        try:
            return ImageFont.truetype(name, size)
        except Exception:
            continue
    return ImageFont.load_default()


def render_card(
    spec: L.CardSpec,
    inscricao: str,
    respostas: dict | None = None,
    scale: float = 1.0,
    titulo: str = "CARTÃO-RESPOSTA",
    subtitulo: str = "",
) -> Image.Image:
    respostas = respostas or {}
    inscricao = str(inscricao)

    W = int(L.W * scale)
    H = int(L.H * scale)
    img = Image.new("L", (W, H), 255)
    d = ImageDraw.Draw(img)

    def S(v):  # escala um valor canônico
        return v * scale

    # --- moldura leve ---
    d.rectangle([S(20), S(20), W - S(20), H - S(20)], outline=0, width=max(1, int(S(2))))

    # --- fiduciais ---
    fids = L.fiducial_centers()
    half = L.FID / 2
    for (cx, cy) in fids.values():
        d.rectangle(
            [S(cx - half), S(cy - half), S(cx + half), S(cy + half)],
            fill=0,
        )
    # barra de orientação
    ox0, oy0, ox1, oy1 = L.orient_bar()
    d.rectangle([S(ox0), S(oy0), S(ox1), S(oy1)], fill=0)

    # --- cabeçalho ---
    fh = _font(int(S(30)))
    fs = _font(int(S(18)))
    d.text((S(L.W * 0.5), S(L.MARGIN + 4)), titulo, fill=0, font=fh, anchor="mm")
    if subtitulo:
        d.text((S(L.W * 0.5), S(L.MARGIN + 34)), subtitulo, fill=0, font=fs, anchor="mm")

    # --- bloco de ID (bolhas) ---
    idx0, idy0, idx1, idy1 = L.id_header_box(spec.id_digits)
    d.rectangle([S(idx0), S(idy0), S(idx1), S(idy1)], outline=0, width=max(1, int(S(1))))
    flbl = _font(int(S(15)))
    d.text((S(idx0 + 4), S(idy0 - 20)), "INSCRIÇÃO", fill=0, font=flbl)
    # número humano-legível
    d.text((S(idx1 - 4), S(idy0 - 20)), inscricao, fill=0, font=flbl, anchor="ra")

    # garante largura certa do identificador (zero-padding à esquerda)
    digits = inscricao.rjust(spec.id_digits, "0")[-spec.id_digits:]
    fdig = _font(int(S(13)))
    for b in spec.id_bubbles():
        filled = digits[b.col] == b.value
        _bubble(d, b.cx, b.cy, b.r, scale, filled=filled)
        if b.value == "0":  # rótulo de dígito no topo de cada coluna não; rótulo lateral 0..9
            pass
    # rótulos 0..9 à esquerda da primeira coluna
    for dv in range(10):
        cy = L.ID_TOP + dv * L.ID_ROW_PITCH
        d.text((S(L.ID_LEFT - 24), S(cy)), str(dv), fill=0, font=fdig, anchor="rm")

    # --- grade de respostas ---
    fq = _font(int(S(16)))
    fo = _font(int(S(13)))
    for q in range(1, spec.num_questoes + 1):
        lx, ly = L.question_label_pos(q)
        d.text((S(lx), S(ly)), f"{q:02d}", fill=0, font=fq, anchor="lm")
    for b in spec.answer_bubbles():
        marked = str(respostas.get(b.col, respostas.get(str(b.col), ""))).upper() == b.value
        _bubble(d, b.cx, b.cy, b.r, scale, filled=marked, letter=b.value, font=fo)

    # rodapé
    ff = _font(int(S(13)))
    d.text((S(L.W * 0.5), H - S(30)),
           "Preencha a bolha completamente com caneta preta ou azul.",
           fill=0, font=ff, anchor="mm")
    return img


def _bubble(d: ImageDraw.ImageDraw, cx, cy, r, scale, filled=False, letter="", font=None):
    x0, y0 = (cx - r) * scale, (cy - r) * scale
    x1, y1 = (cx + r) * scale, (cy + r) * scale
    if filled:
        d.ellipse([x0, y0, x1, y1], fill=0, outline=0, width=max(1, int(2 * scale)))
    else:
        d.ellipse([x0, y0, x1, y1], outline=0, width=max(1, int(1.5 * scale)))
        if letter and font is not None:
            d.text((cx * scale, cy * scale), letter, fill=0, font=font, anchor="mm")
