"""
Geometria canônica do cartão-resposta Certamme (fonte única de verdade).

O MESMO módulo é usado para DESENHAR o cartão (render.py) e para LER o cartão
(reader.py). Assim gerador e leitor nunca divergem: um único conjunto de
coordenadas descreve fiduciais, bloco de identificação e grade de respostas.

Sistema de coordenadas: canvas "canônico" em pixels (retrato, proporção ~A4).
O leitor detecta os 4 fiduciais na foto, aplica homografia para este canvas e
amostra as bolhas exatamente nestas coordenadas.
"""
from __future__ import annotations

from dataclasses import dataclass, field


# --- Canvas canônico (retrato, ~proporção A4 1:1.414) ---
W = 1000
H = 1414

# --- Fiduciais: 4 quadrados sólidos nos cantos + barra de orientação no topo ---
MARGIN = 55          # margem do canvas até a borda externa do fiducial
FID = 46             # lado do quadrado fiducial
# marca de orientação (assimétrica): retângulo preto no topo, deslocado à esquerda
ORIENT_W = 150
ORIENT_H = 20
ORIENT_CY = MARGIN + FID + 26   # y do centro da barra de orientação


def fiducial_centers() -> dict[str, tuple[int, int]]:
    half = FID / 2
    return {
        "TL": (MARGIN + half, MARGIN + half),
        "TR": (W - MARGIN - half, MARGIN + half),
        "BL": (MARGIN + half, H - MARGIN - half),
        "BR": (W - MARGIN - half, H - MARGIN - half),
    }


def orient_bar() -> tuple[int, int, int, int]:
    """Retângulo (x0,y0,x1,y1) da barra de orientação — fica à ESQUERDA do centro."""
    cx = W * 0.36
    return (
        int(cx - ORIENT_W / 2), int(ORIENT_CY - ORIENT_H / 2),
        int(cx + ORIENT_W / 2), int(ORIENT_CY + ORIENT_H / 2),
    )


@dataclass
class Bubble:
    cx: float
    cy: float
    r: float
    # metadados do que esta bolha representa
    kind: str                 # "id" | "answer"
    col: int = 0              # id: posição do dígito | answer: nº da questão (1-based)
    value: str = ""           # id: dígito "0".."9" | answer: opção "A".."E"


# --- Bloco de identificação (nº de inscrição em bolhas, uma coluna por dígito) ---
ID_TOP = ORIENT_CY + 60       # topo do bloco de dígitos
ID_LEFT = 130
ID_COL_PITCH = 58             # distância horizontal entre colunas de dígitos
ID_ROW_PITCH = 29             # distância vertical entre dígitos 0..9
ID_R = 11                     # raio da bolha de dígito


def id_bubbles(id_digits: int) -> list[Bubble]:
    out: list[Bubble] = []
    for c in range(id_digits):
        cx = ID_LEFT + c * ID_COL_PITCH
        for d in range(10):
            cy = ID_TOP + d * ID_ROW_PITCH
            out.append(Bubble(cx=cx, cy=cy, r=ID_R, kind="id", col=c, value=str(d)))
    return out


def id_header_box(id_digits: int) -> tuple[int, int, int, int]:
    """Caixa (x0,y0,x1,y1) ao redor do bloco de ID, p/ rótulos no render."""
    x0 = ID_LEFT - 24
    x1 = ID_LEFT + (id_digits - 1) * ID_COL_PITCH + 24
    y0 = ID_TOP - 34
    y1 = ID_TOP + 9 * ID_ROW_PITCH + 22
    return (int(x0), int(y0), int(x1), int(y1))


# --- Grade de respostas: colunas de questões, cada questão com N opções A..E ---
ANS_TOP = ID_TOP + 10 * ID_ROW_PITCH + 70   # começa abaixo do bloco de ID
ANS_LEFT = 120
ANS_ROW_PITCH = 34            # distância vertical entre questões
ANS_OPT_PITCH = 40           # distância horizontal entre opções A,B,C..
ANS_R = 12                   # raio da bolha de opção
ANS_NUM_GAP = 46             # espaço antes da 1ª opção (p/ o número da questão)
ANS_COL_GAP = 300            # largura total de uma coluna de questões
QUESTIONS_PER_COL = 25       # questões por coluna antes de quebrar p/ a direita

LETTERS = "ABCDEFGHIJ"


def answer_bubbles(num_questoes: int, num_opcoes: int) -> list[Bubble]:
    out: list[Bubble] = []
    for q in range(1, num_questoes + 1):
        idx = q - 1
        col = idx // QUESTIONS_PER_COL
        row = idx % QUESTIONS_PER_COL
        base_x = ANS_LEFT + col * ANS_COL_GAP + ANS_NUM_GAP
        cy = ANS_TOP + row * ANS_ROW_PITCH
        for o in range(num_opcoes):
            cx = base_x + o * ANS_OPT_PITCH
            out.append(Bubble(cx=cx, cy=cy, r=ANS_R, kind="answer", col=q, value=LETTERS[o]))
    return out


def question_label_pos(q: int) -> tuple[int, int]:
    idx = q - 1
    col = idx // QUESTIONS_PER_COL
    row = idx % QUESTIONS_PER_COL
    x = ANS_LEFT + col * ANS_COL_GAP
    y = ANS_TOP + row * ANS_ROW_PITCH
    return (int(x), int(y))


@dataclass
class CardSpec:
    num_questoes: int = 50
    num_opcoes: int = 5
    id_digits: int = 6

    def id_bubbles(self) -> list[Bubble]:
        return id_bubbles(self.id_digits)

    def answer_bubbles(self) -> list[Bubble]:
        return answer_bubbles(self.num_questoes, self.num_opcoes)

    def all_bubbles(self) -> list[Bubble]:
        return self.id_bubbles() + self.answer_bubbles()
