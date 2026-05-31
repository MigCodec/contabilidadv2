<?php

use App\Http\Controllers\AsientoController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Seguridad\PermisoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->hasPermission('cuentas.gestionar')) {
        return redirect()->route('cuentas.index');
    }

    if ($user->hasPermission('asientos.gestionar')) {
        return redirect()->route('asientos.index');
    }

    if ($user->hasPermission('reportes.ver')) {
        return redirect()->route('reportes.libro-mayor');
    }

    if ($user->hasPermission('permisos.gestionar')) {
        return redirect()->route('seguridad.permisos.index');
    }

    return redirect()->route('sin-permisos');
})->name('inicio');

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
    Route::view('/sin-permisos', 'auth.sin-permisos')->name('sin-permisos');

    Route::resource('cuentas', CuentaController::class)->middleware('permiso:cuentas.gestionar');
    Route::resource('asientos', AsientoController::class)->middleware('permiso:asientos.gestionar');

    Route::prefix('reportes')->name('reportes.')->middleware('permiso:reportes.ver')->group(function (): void {
        Route::get('/libro-mayor', [ReporteController::class, 'libroMayor'])->name('libro-mayor');
        Route::get('/balance-general', [ReporteController::class, 'balanceGeneral'])->name('balance-general');
        Route::get('/balance-general-extendido', [ReporteController::class, 'balanceGeneralExtendido'])->name('balance-general-extendido');
    });

    Route::prefix('seguridad')->name('seguridad.')->middleware('permiso:permisos.gestionar')->group(function (): void {
        Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
        Route::put('/usuarios/{user}/roles', [PermisoController::class, 'actualizarRoles'])->name('usuarios.roles.update');
        Route::put('/roles/{role}/permisos', [PermisoController::class, 'actualizarPermisos'])->name('roles.permisos.update');
    });
});
