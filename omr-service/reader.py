"""
Leitor OMR do cartão-resposta Certamme (OpenCV puro).

Pipeline:
  1. Localiza os 4 fiduciais de canto (quadrados sólidos).
  2. Homografia da foto -> canvas canônico (layout.W x layout.H).
  3. Corrige rotação de 180° pela barra de orientação (assimétrica).
  4. Binariza e amostra cada bolha nas coordenadas canônicas.
  5. Decide a opção marcada por questão (com flags de múltipla/branco/dúvida)
     e lê o nº de inscrição pré-impresso no bloco de ID.

Saída: dict {inscricao, respostas{q:op}, questoes[...], flags[...], ok, ...}
"""
from __future__ import annotations

from dataclasses import dataclass, field

import cv2
import numpy as np

import layout as L


# --- limiares de decisão (fração de pixels escuros dentro da bolha, 0..1) ---
FILL_MIN = 0.45      # abaixo disso a bolha é considerada vazia
MARGIN_MIN = 0.18    # diferença mínima entre a melhor e a 2ª p/ ter certeza
MULTI_MIN = 0.45     # 2ª opção acima disso => marcação múltipla


class OmrError(Exception):
    pass


@dataclass
class QuestaoResultado:
    numero: int
    resposta: str
    status: str                 # ok | branco | multipla | duvida
    scores: dict = field(default_factory=dict)


def ler_cartao(
    image: np.ndarray,
    num_questoes: int = 50,
    num_opcoes: int = 5,
    id_digits: int = 6,
    threshold: int | None = None,
) -> dict:
    spec = L.CardSpec(num_questoes=num_questoes, num_opcoes=num_opcoes, id_digits=id_digits)

    gray = _to_gray(image)
    quad = _find_fiducials(gray)
    warped = _warp(gray, quad)
    warped = _fix_orientation(warped)

    binimg = _binarize(warped)

    fill_min = (threshold / 100.0) if threshold else FILL_MIN

    # --- ID ---
    inscricao, id_conf, id_flags = _read_id(binimg, spec)

    # --- respostas ---
    questoes: list[QuestaoResultado] = []
    respostas: dict[str, str] = {}
    flags: list[str] = list(id_flags)
    answers = spec.answer_bubbles()
    by_q: dict[int, list[L.Bubble]] = {}
    for b in answers:
        by_q.setdefault(b.col, []).append(b)

    for q in sorted(by_q):
        scores = {b.value: _fill_score(binimg, b.cx, b.cy, b.r) for b in by_q[q]}
        ordered = sorted(scores.items(), key=lambda kv: kv[1], reverse=True)
        best_op, best = ordered[0]
        second = ordered[1][1] if len(ordered) > 1 else 0.0

        if best < fill_min:
            status, resp = "branco", ""
        elif second >= MULTI_MIN:
            status, resp = "multipla", ""
        elif (best - second) < MARGIN_MIN:
            status, resp = "duvida", best_op
        else:
            status, resp = "ok", best_op

        questoes.append(QuestaoResultado(q, resp, status, {k: round(v, 3) for k, v in scores.items()}))
        if resp:
            respostas[str(q)] = resp
        if status != "ok":
            flags.append(f"q{q}:{status}")

    ok = inscricao is not None and len([f for f in flags if not f.startswith("q")]) == 0
    return {
        "ok": ok,
        "inscricao": inscricao,
        "id_confianca": round(id_conf, 3),
        "respostas": respostas,
        "questoes": [q.__dict__ for q in questoes],
        "flags": flags,
        "num_questoes": num_questoes,
        "num_opcoes": num_opcoes,
    }


# ---------------------------------------------------------------- etapas

def _to_gray(image: np.ndarray) -> np.ndarray:
    if image.ndim == 3:
        return cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    return image


def _find_fiducials(gray: np.ndarray) -> np.ndarray:
    """Devolve os 4 centros dos fiduciais ordenados [TL, TR, BR, BL]."""
    h, w = gray.shape
    blur = cv2.GaussianBlur(gray, (5, 5), 0)
    th = cv2.adaptiveThreshold(blur, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                               cv2.THRESH_BINARY_INV, 41, 10)
    th = cv2.morphologyEx(th, cv2.MORPH_CLOSE, np.ones((5, 5), np.uint8))

    # RETR_LIST (não EXTERNAL): a moldura do cartão é o contorno externo e
    # esconderia os fiduciais como filhos. Pegamos todos e filtramos por forma.
    cnts, _ = cv2.findContours(th, cv2.RETR_LIST, cv2.CHAIN_APPROX_SIMPLE)
    img_area = h * w
    cands = []
    for c in cnts:
        area = cv2.contourArea(c)
        if area < img_area * 0.0003 or area > img_area * 0.03:
            continue
        x, y, bw, bh = cv2.boundingRect(c)
        if bh == 0:
            continue
        aspect = bw / bh
        if not (0.6 <= aspect <= 1.6):
            continue
        extent = area / (bw * bh)        # quão "cheio" é o contorno
        if extent < 0.82:               # quadrado sólido ~1.0; anel de bolha ~0.785
            continue
        cx, cy = x + bw / 2, y + bh / 2
        cands.append((cx, cy, area))

    if len(cands) < 4:
        raise OmrError(f"fiduciais insuficientes: achei {len(cands)} (esperado 4)")

    pts = np.array([(c[0], c[1]) for c in cands], dtype=np.float32)
    # 4 extremos de canto
    s = pts.sum(axis=1)
    diff = pts[:, 0] - pts[:, 1]
    tl = pts[np.argmin(s)]
    br = pts[np.argmax(s)]
    tr = pts[np.argmax(diff)]
    bl = pts[np.argmin(diff)]
    quad = np.array([tl, tr, br, bl], dtype=np.float32)

    # sanidade: os 4 cantos devem ser distintos
    if len({tuple(np.round(p)) for p in quad}) < 4:
        raise OmrError("não consegui isolar 4 cantos distintos")
    return quad


def _warp(gray: np.ndarray, quad: np.ndarray) -> np.ndarray:
    fids = L.fiducial_centers()
    dst = np.array([fids["TL"], fids["TR"], fids["BR"], fids["BL"]], dtype=np.float32)
    M = cv2.getPerspectiveTransform(quad, dst)
    return cv2.warpPerspective(gray, M, (L.W, L.H), flags=cv2.INTER_AREA,
                               borderValue=255)


def _fix_orientation(warped: np.ndarray) -> np.ndarray:
    """Barra de orientação deve estar escura na posição esperada; senão gira 180°."""
    def bar_darkness(img):
        x0, y0, x1, y1 = L.orient_bar()
        roi = img[y0:y1, x0:x1]
        if roi.size == 0:
            return 1.0
        return float(np.mean(roi))  # menor = mais escuro

    upright = bar_darkness(warped)
    rotated = cv2.rotate(warped, cv2.ROTATE_180)
    rot_bar = bar_darkness(rotated)
    # escolhe a orientação onde a barra está mais escura
    return warped if upright <= rot_bar else rotated


def _binarize(warped: np.ndarray) -> np.ndarray:
    """Binário invertido: pixel marcado = 1 (255)."""
    blur = cv2.GaussianBlur(warped, (3, 3), 0)
    _, bw = cv2.threshold(blur, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)
    return bw


def _fill_score(binimg: np.ndarray, cx: float, cy: float, r: float) -> float:
    """Fração de pixels marcados dentro de um disco de raio r*0.72 na bolha."""
    rr = max(3, int(r * 0.72))
    x0, y0 = int(cx - rr), int(cy - rr)
    x1, y1 = int(cx + rr), int(cy + rr)
    h, w = binimg.shape
    if x0 < 0 or y0 < 0 or x1 > w or y1 > h:
        return 0.0
    patch = binimg[y0:y1, x0:x1]
    mask = np.zeros(patch.shape, np.uint8)
    cv2.circle(mask, (patch.shape[1] // 2, patch.shape[0] // 2), rr, 255, -1)
    sel = patch[mask == 255]
    if sel.size == 0:
        return 0.0
    return float(np.count_nonzero(sel) / sel.size)


def _read_id(binimg: np.ndarray, spec: L.CardSpec):
    """Lê o nº de inscrição pré-impresso (1 marca por coluna de dígito)."""
    cols: dict[int, list[L.Bubble]] = {}
    for b in spec.id_bubbles():
        cols.setdefault(b.col, []).append(b)

    digits = []
    confs = []
    flags = []
    for c in sorted(cols):
        scores = [(b.value, _fill_score(binimg, b.cx, b.cy, b.r)) for b in cols[c]]
        scores.sort(key=lambda kv: kv[1], reverse=True)
        best_v, best = scores[0]
        second = scores[1][1] if len(scores) > 1 else 0.0
        digits.append(best_v)
        confs.append(best - second)
        if best < 0.35:
            flags.append(f"id_col{c}:fraco")

    inscricao = "".join(digits).lstrip("0") or "0"
    conf = float(np.mean(confs)) if confs else 0.0
    if conf < 0.1:
        flags.append("id:baixa_confianca")
    return inscricao, conf, flags
