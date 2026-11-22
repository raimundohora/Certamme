<?php

use App\Http\Controllers\Api\ConcursoApiController;
use Illuminate\Support\Facades\Route;

/*
 | API pública do Certamme — por organizadora, autenticada por token bearer
 | (middleware api.tenant resolve o tenant a partir do token). Somente leitura.
 | Base: /api/v1
 */
Route::middleware(['api.tenant', 'throttle:120,1'])->prefix('v1')->group(function () {
    Route::get('/me', [ConcursoApiController::class, 'me']);
    Route::get('/concursos', [ConcursoApiController::class, 'index']);
    Route::get('/concursos/{concurso}', [ConcursoApiController::class, 'show'])->whereNumber('concurso');
    Route::get('/concursos/{concurso}/inscritos', [ConcursoApiController::class, 'inscritos'])->whereNumber('concurso');
    Route::get('/concursos/{concurso}/resultados', [ConcursoApiController::class, 'resultados'])->whereNumber('concurso');
});
