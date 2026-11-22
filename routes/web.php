<?php

use App\Http\Controllers\Portal\AuthController;
use App\Http\Controllers\Portal\ConcursoController;
use App\Http\Controllers\Portal\HomeController;
use App\Http\Controllers\Portal\DocumentoController;
use App\Http\Controllers\Portal\InscricaoController;
use App\Http\Controllers\Portal\IsencaoController;
use App\Http\Controllers\Portal\RecursoController;
use App\Http\Controllers\Portal\PainelController;
use App\Http\Controllers\Portal\ResultadoController;
use App\Http\Controllers\Portal\CertificadoController;
use App\Http\Controllers\Portal\ClassificacaoController;
use App\Http\Controllers\Admin\KitSalaController;
use App\Http\Controllers\Admin\RelatorioController;
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
        Route::get('/inscricao/{inscricao}/certificado/{tipo}', [CertificadoController::class, 'emitir'])->name('portal.certificado');
        Route::post('/inscricao/{inscricao}/documentos', [DocumentoController::class, 'store'])->name('portal.documento.store');
        Route::post('/inscricao/{inscricao}/isencao', [IsencaoController::class, 'store'])->name('portal.isencao.store');
        Route::post('/inscricao/{inscricao}/recurso', [RecursoController::class, 'store'])->name('portal.recurso.store');
    });

    Route::get('/concursos/{concurso}', [ConcursoController::class, 'show'])->name('portal.concurso');
    Route::get('/concursos/{concurso}/classificacao', [ClassificacaoController::class, 'concurso'])->name('portal.classificacao');
    Route::get('/concursos/{concurso}/classificacao/pdf', [ClassificacaoController::class, 'pdf'])->name('portal.classificacao.pdf');

    // Autenticação pública de documentos (sem login): /autenticar e /autenticar/{codigo}
    Route::get('/autenticar/{codigo?}', [CertificadoController::class, 'verificar'])->name('portal.autenticar');
});

// Kit de sala (admin autenticado)
Route::middleware(['web', 'auth'])->get('/kit/{etapa}/lista-presenca', [KitSalaController::class, 'listaPresenca'])->name('kit.presenca');
Route::middleware(['web', 'auth'])->get('/kit/{etapa}/mapa-sala', [KitSalaController::class, 'mapaSala'])->name('kit.mapa');
Route::middleware(['web', 'auth'])->get('/kit/{etapa}/ata-sala', [KitSalaController::class, 'ata'])->name('kit.ata');
Route::middleware(['web', 'auth'])->get('/kit/{etapa}/cartoes-resposta', [KitSalaController::class, 'cartoes'])->name('kit.cartoes');

// Relatorios/exportacoes (admin autenticado)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/relatorios/classificados/{etapa}', [RelatorioController::class, 'classificados'])->name('rel.classificados');
    Route::get('/relatorios/arrecadacao/{concurso}', [RelatorioController::class, 'arrecadacao'])->name('rel.arrecadacao');
});

// Webhook do gateway de pagamento (Asaas) - sem auth/tenant
Route::post('/webhooks/asaas', \App\Http\Controllers\AsaasWebhookController::class)->name('webhooks.asaas');
