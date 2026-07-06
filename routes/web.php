<?php

/*
|--------------------------------------------------------------------------
| Rutas web de la aplicación
|--------------------------------------------------------------------------
| Este archivo define las URL que el usuario puede visitar en el navegador.
| Cada ruta apunta a un método de controlador que procesa la solicitud.
| Las rutas se organizan por secciones de autenticación y roles.
*/

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProspectoController;
use App\Http\Controllers\VoiceChatController;
use App\Http\Controllers\FavoritoController;

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendForgotPassword'])->name('password.email');
Route::get('/forgot-password/verify', [AuthController::class, 'showVerifyCode'])->name('password.verify');
Route::post('/forgot-password/verify', [AuthController::class, 'verifyCode'])->name('password.verify.post');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMINISTRADOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->name('dashboard');

        // Comando manual desde el dashboard administrador
        Route::post('/comando', [DashboardController::class, 'comando'])
            ->name('comando');

        // Propiedades
        Route::get('/propiedades', [PropiedadController::class, 'index'])
            ->name('propiedades');

        Route::post('/propiedades', [PropiedadController::class, 'store'])
            ->name('propiedades.store');

        Route::get('/buscar', [PropiedadController::class, 'buscarAdmin'])
            ->name('buscar');

        Route::put('/propiedades/{propiedad}', [PropiedadController::class, 'update'])
            ->name('propiedades.update');

        Route::delete('/propiedades/{propiedad}', [PropiedadController::class, 'destroy'])
            ->name('propiedades.destroy');

        // Usuarios
        Route::get('/usuarios', [UsuarioController::class, 'index'])
            ->name('usuarios');

        Route::post('/usuarios', [UsuarioController::class, 'store'])
            ->name('usuarios.store');

        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])
            ->name('usuarios.update');

        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
            ->name('usuarios.destroy');

        // Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes');
        Route::get('/reportes/export/{type}', [ReporteController::class, 'export'])
            ->name('reportes.export');
    });

/*
|--------------------------------------------------------------------------
| AGENTE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:agente,administrador'])
    ->prefix('agente')
    ->name('agente.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'agente'])
            ->name('dashboard');

        Route::get('/propiedades', [PropiedadController::class, 'index'])
            ->name('propiedades');

        Route::get('/buscar', [PropiedadController::class, 'buscarAgente'])
            ->name('buscar');

        Route::post('/propiedades', [PropiedadController::class, 'store'])
            ->name('propiedades.store');

        Route::put('/propiedades/{propiedad}', [PropiedadController::class, 'update'])
            ->name('propiedades.update');

        Route::delete('/propiedades/{propiedad}', [PropiedadController::class, 'destroy'])
            ->name('propiedades.destroy');

        Route::get('/visitas', [SolicitudController::class, 'visitasAgente'])
            ->name('visitas');

        Route::resource('prospectos', ProspectoController::class)->except(['show']);

        Route::put('/visitas/{solicitud}/estado', [SolicitudController::class, 'actualizarEstado'])
            ->name('visitas.estado');

        Route::get('/clientes', [SolicitudController::class, 'clientesAgente'])
            ->name('clientes');

        Route::get('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'index'])
            ->name('clientes.seguimientos');
        Route::post('/clientes/{cliente}/seguimientos', [SeguimientoController::class, 'store'])
            ->name('clientes.seguimientos.store');

        Route::get('/calendario', [SolicitudController::class, 'calendarioAgente'])
            ->name('calendario');

        Route::get('/calendario/eventos', [SolicitudController::class, 'eventosAgente'])
            ->name('calendario.eventos');
    });

/*
|--------------------------------------------------------------------------
| ASISTENTE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:asistente,administrador'])
    ->prefix('asistente')
    ->name('asistente.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'asistente'])
            ->name('dashboard');

        Route::get('/buscar', [PropiedadController::class, 'buscarAsistente'])
            ->name('buscar');

        Route::get('/visitas', [SolicitudController::class, 'visitasAsistente'])
            ->name('visitas');

        Route::put('/visitas/{id}/estado',
            [SolicitudController::class, 'cambiarEstado'])
            ->name('visitas.estado');

        Route::get('/reportes', [ReporteController::class, 'index'])
            ->name('reportes');
        Route::get('/reportes/export/{type}', [ReporteController::class, 'export'])
            ->name('reportes.export');

        Route::get('/calendario', [SolicitudController::class, 'calendarioAsistente'])
            ->name('calendario');
        Route::get('/calendario/eventos', [SolicitudController::class, 'eventosAsistente'])
            ->name('calendario.eventos');
    });

/*
|--------------------------------------------------------------------------
| CLIENTE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('cliente')
    ->name('cliente.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'cliente'])
            ->name('dashboard');

        Route::get('/propiedades', [PropiedadController::class, 'disponibles'])
            ->name('propiedades');

        Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'detalle'])
            ->name('propiedades.detalle');

        // CU8: Buscar propiedades
        Route::get('/buscar', [PropiedadController::class, 'buscar'])
            ->name('buscar');

        Route::post('/solicitudes', [SolicitudController::class, 'store'])
            ->name('solicitudes.store');

        Route::patch('/solicitudes/{id}/cancelar', [SolicitudController::class, 'cancelar'])
            ->name('solicitudes.cancelar');

        Route::patch('/solicitudes/{id}/reagendar', [SolicitudController::class, 'reagendar'])
            ->name('solicitudes.reagendar');

        Route::get('/mis-solicitudes', [SolicitudController::class, 'misSolicitudes'])
            ->name('solicitudes');

        Route::post('/propiedades/{propiedad}/resenas', [PropiedadController::class, 'storeResena'])
            ->name('propiedades.resenas.store');

        Route::get('/calendario', [SolicitudController::class, 'calendarioCliente'])
            ->name('calendario');
        Route::get('/calendario/eventos', [SolicitudController::class, 'eventosCliente'])
            ->name('calendario.eventos');

        // CU22: Favoritos
        Route::get('/mis-favoritos', [FavoritoController::class, 'index'])
            ->name('favoritos.index');
        Route::post('/favoritos/{propiedad}/toggle', [FavoritoController::class, 'toggle'])
            ->name('favoritos.toggle');
        Route::delete('/favoritos/{propiedad}', [FavoritoController::class, 'destroy'])
            ->name('favoritos.destroy');
    });

/*
|--------------------------------------------------------------------------
| PERFIL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/perfil', [UsuarioController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [UsuarioController::class, 'actualizarPerfil'])
        ->name('perfil.update');

    // Chat de voz AI
    Route::post('/voice-chat', [VoiceChatController::class, 'process'])
        ->name('voice-chat.process');
});