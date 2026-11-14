<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\ConcursoController;
use App\Http\Controllers\Portal\HomeController;
use App\Http\Controllers\Portal\InscricaoController;
use App\Http\Controllers\Portal\PainelController;
use App\Http\Controllers\Portal\ResultadoController;
use Illuminate\Support\Facades\Route;

Route::middleware('portal.tenant')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('portal.home');
    Route::get('/resultado', [ResultadoController::class, 'form'])->name('portal.resultado');
    Route::post('/resultado', [ResultadoController::class, 'consultar'])->name('portal.resultado.consultar');

    Route::get('/entrar', [AuthController::class, 'showLogin'])->name('portal.login');
    Route::post('/entrar', [AuthController::class, 'login']);
    Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('portal.register');
    Route::post('/cadastro', [AuthController::class, 'register']);
    Route::post('/sair', [AuthController::class, 'logout'])->name('portal.logout');

    Route::middleware('auth:candidato')->group(function () {
        Route::get('/perfil', [AuthController::class, 'showPerfil'])->name('portal.perfil');
        Route::post('/perfil', [AuthController::class, 'savePerfil'])->name('portal.perfil.save');
        Route::get('/minha-area', [PainelController::class, 'index'])->name('portal.painel');
        Route::get('/concursos/{concurso}/inscrever', [InscricaoController::class, 'create'])->name('portal.inscrever');
        Route::post('/concursos/{concurso}/inscrever', [InscricaoController::class, 'store']);
        Route::get('/inscricao/{inscricao}/comprovante', [InscricaoController::class, 'comprovante'])->name('portal.comprovante');
    });

    Route::get('/concursos/{concurso}', [ConcursoController::class, 'show'])->name('portal.concurso');
});
