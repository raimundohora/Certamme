"""
Serviço OMR do Certamme (FastAPI).

Endpoints:
  GET  /health                 -> {"status":"ok"}
  POST /read    (multipart)    -> lê um cartão (imagem) e devolve respostas/inscrição
  POST /cartoes (json)         -> gera um PDF com 1 cartão por candidato (p/ impressão)

Chamado internamente pelo container Laravel na rede certamme_sail
(http://omr-service:8000). Também publicado em 127.0.0.1:9108 p/ depuração.
"""
from __future__ import annotations

import io

import cv2
import numpy as np
from fastapi import FastAPI, File, Form, UploadFile
from fastapi.responses import JSONResponse, Response
from pydantic import BaseModel

import layout as L
import reader
from render import render_card

app = FastAPI(title="Certamme OMR", version="1.0")


@app.get("/health")
def health():
    return {"status": "ok", "service": "certamme-omr"}


@app.post("/read")
async def read_card(
    file: UploadFile = File(...),
    num_questoes: int = Form(50),
    num_opcoes: int = Form(5),
    id_digits: int = Form(6),
    threshold: int = Form(0),
):
    raw = await file.read()
    arr = np.frombuffer(raw, np.uint8)
    img = cv2.imdecode(arr, cv2.IMREAD_GRAYSCALE)
    if img is None:
        return JSONResponse({"ok": False, "erro": "imagem inválida"}, status_code=400)
    try:
        out = reader.ler_cartao(
            img,
            num_questoes=num_questoes,
            num_opcoes=num_opcoes,
            id_digits=id_digits,
            threshold=threshold or None,
        )
    except reader.OmrError as e:
        return JSONResponse({"ok": False, "erro": str(e)}, status_code=422)
    out["arquivo"] = file.filename
    return out


class Candidato(BaseModel):
    inscricao: str
    nome: str | None = None


class CartoesReq(BaseModel):
    candidatos: list[Candidato]
    num_questoes: int = 50
    num_opcoes: int = 5
    id_digits: int = 6
    titulo: str = "CARTÃO-RESPOSTA"
    subtitulo: str = ""


@app.post("/cartoes")
def gerar_cartoes(req: CartoesReq):
    spec = L.CardSpec(num_questoes=req.num_questoes, num_opcoes=req.num_opcoes, id_digits=req.id_digits)
    # ~200 DPI em A4: 1000px canônico * 1.65 = 1654px ≈ 210mm
    scale = 1.65
    imgs = []
    for c in req.candidatos:
        sub = req.subtitulo
        if c.nome:
            sub = (sub + "  •  " if sub else "") + c.nome
        card = render_card(spec, c.inscricao, respostas=None, scale=scale,
                           titulo=req.titulo, subtitulo=sub)
        imgs.append(card.convert("L"))

    if not imgs:
        return JSONResponse({"erro": "sem candidatos"}, status_code=400)

    buf = io.BytesIO()
    imgs[0].save(buf, format="PDF", save_all=True, append_images=imgs[1:],
                 resolution=200.0)
    buf.seek(0)
    return Response(content=buf.read(), media_type="application/pdf",
                    headers={"Content-Disposition": 'inline; filename="cartoes-omr.pdf"'})
