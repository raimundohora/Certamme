"""
Teste de laço fechado do OMR: gera -> simula foto -> lê -> confere.

Prova a robustez do leitor sob perspectiva/ruído/rotação, com a geometria de
geração e leitura vindo da MESMA fonte (layout.py).
"""
from __future__ import annotations

import random
import sys

import cv2
import numpy as np

import layout as L
import reader
from render import render_card

LETTERS = "ABCDE"


def simular_foto(img_pil, perspectiva=0.03, ruido=6, girar180=False, seed=0):
    """Converte o cartão PIL em 'foto': borda, perspectiva, blur, ruído, brilho."""
    rng = random.Random(seed)
    img = np.array(img_pil)                         # grayscale
    h, w = img.shape

    # borda (fundo claro) p/ fiduciais não encostarem no limite
    pad = int(0.06 * w)
    img = cv2.copyMakeBorder(img, pad, pad, pad, pad, cv2.BORDER_CONSTANT, value=245)
    h, w = img.shape

    # perspectiva aleatória
    d = perspectiva
    src = np.float32([[0, 0], [w, 0], [w, h], [0, h]])
    jit = lambda: rng.uniform(-d, d)
    dst = np.float32([
        [w * jit(), h * jit()],
        [w * (1 + jit()), h * jit()],
        [w * (1 + jit()), h * (1 + jit())],
        [w * jit(), h * (1 + jit())],
    ])
    M = cv2.getPerspectiveTransform(src, dst)
    img = cv2.warpPerspective(img, M, (w, h), borderValue=245)

    img = cv2.GaussianBlur(img, (3, 3), 0)
    # variação de brilho/contraste
    alpha = rng.uniform(0.9, 1.1)
    beta = rng.uniform(-12, 12)
    img = np.clip(img.astype(np.float32) * alpha + beta, 0, 255).astype(np.uint8)
    # ruído
    if ruido:
        noise = np.random.default_rng(seed).normal(0, ruido, img.shape)
        img = np.clip(img.astype(np.float32) + noise, 0, 255).astype(np.uint8)
    if girar180:
        img = cv2.rotate(img, cv2.ROTATE_180)
    return img


def trial(seed: int, nq=50, nop=5, iddig=6, girar=False):
    rng = random.Random(seed)
    inscricao = str(rng.randint(1, 10 ** iddig - 1))
    respostas = {q: LETTERS[rng.randrange(nop)] for q in range(1, nq + 1)}
    spec = L.CardSpec(num_questoes=nq, num_opcoes=nop, id_digits=iddig)
    card = render_card(spec, inscricao, respostas, scale=1.6, subtitulo="Concurso X — Cargo Y")
    foto = simular_foto(card, perspectiva=0.025, ruido=6, girar180=girar, seed=seed)
    out = reader.ler_cartao(foto, num_questoes=nq, num_opcoes=nop, id_digits=iddig)

    # confere inscrição
    id_ok = out["inscricao"] == inscricao
    # confere respostas
    erradas = [q for q in range(1, nq + 1)
               if out["respostas"].get(str(q), "") != respostas[q]]
    return id_ok, erradas, out, inscricao, respostas


def main():
    N = 25
    falhas = 0
    tot_erradas = 0
    for seed in range(N):
        girar = (seed % 5 == 0)   # 1 em cada 5 de cabeça p/ baixo
        try:
            id_ok, erradas, out, insc, resp = trial(seed, girar=girar)
        except reader.OmrError as e:
            print(f"[seed {seed}] ERRO de leitura: {e}")
            falhas += 1
            continue
        tot_erradas += len(erradas)
        status = "OK " if (id_ok and not erradas) else "FALHA"
        if not (id_ok and not erradas):
            falhas += 1
            det = f" id({insc}->{out['inscricao']})" if not id_ok else ""
            print(f"[seed {seed}] {status} girar={girar}{det} erradas={erradas[:8]}")
        else:
            print(f"[seed {seed}] {status} girar={girar} insc={out['inscricao']} 50/50 respostas")

    print(f"\n== {N - falhas}/{N} cartões perfeitos (inscrição + todas as respostas) ==")
    print(f"== {tot_erradas} respostas erradas no total de {N*50} ==")

    # --- casos especiais: múltipla e branco ---
    spec = L.CardSpec()
    resp = {q: "A" for q in range(1, 51)}
    card = render_card(spec, "4242", resp, scale=1.6)
    # força questão 10 com 2 marcas e questão 20 em branco, desenhando/limpando direto
    img = np.array(card)
    sc = 1.6
    # marca extra na questão 10 opção C
    b10c = next(b for b in spec.answer_bubbles() if b.col == 10 and b.value == "C")
    cv2.circle(img, (int(b10c.cx * sc), int(b10c.cy * sc)), int(b10c.r * sc), 0, -1)
    # apaga questão 20 (todas as opções) pintando de branco
    for b in spec.answer_bubbles():
        if b.col == 20:
            cv2.circle(img, (int(b.cx * sc), int(b.cy * sc)), int(b.r * sc + 2), 255, -1)
    from PIL import Image
    foto = simular_foto(Image.fromarray(img), perspectiva=0.015, ruido=4, seed=99)
    out = reader.ler_cartao(foto)
    q10 = next(q for q in out["questoes"] if q["numero"] == 10)
    q20 = next(q for q in out["questoes"] if q["numero"] == 20)
    print(f"\nq10 (2 marcas) status={q10['status']} resp='{q10['resposta']}' (esperado multipla)")
    print(f"q20 (branco)   status={q20['status']} resp='{q20['resposta']}' (esperado branco)")

    esp_ok = q10["status"] == "multipla" and q20["status"] == "branco"
    print(f"casos especiais: {'OK' if esp_ok else 'FALHA'}")

    sucesso = falhas == 0 and esp_ok
    print("\nRESULTADO:", "PASSOU ✅" if sucesso else "FALHOU ❌")
    sys.exit(0 if sucesso else 1)


if __name__ == "__main__":
    main()
