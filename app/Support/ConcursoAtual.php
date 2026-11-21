<?php

namespace App\Support;

use App\Models\Concurso;

/**
 * Concurso "selecionado" no painel admin (contexto por sessao) — fluxo tipo ProSeleta:
 * escolhe um concurso e os recursos passam a trabalhar dentro dele.
 */
class ConcursoAtual
{
    public const KEY = 'admin.concurso_id';

    public static function id(): ?int
    {
        $id = session(self::KEY);
        return $id ? (int) $id : null;
    }

    public static function set(int $id): void
    {
        session([self::KEY => $id]);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    public static function model(): ?Concurso
    {
        $id = self::id();
        return $id ? Concurso::find($id) : null;
    }
}
