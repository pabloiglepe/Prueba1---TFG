<?php

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Player\ReservationController;
use App\Http\Controllers\Coach\ClassController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


// Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');


// ANTIGUA | RUTA RAÍZ 
// Route::view('/', 'welcome')->name('home');


// RUTA RAÍZ
Route::get('/', [RedirectController::class, 'home'])->name('home');


// RUTAS AUTENTICADAS -> ACCESIBLES PARA CUALQUIER USUARIO LOGUEADO
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [RedirectController::class, 'home'])
        ->middleware(['auth'])
        ->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});


// RUTAS SOLO PARA ADMIN -> ACCESIBLE SOLO PARA ADMINISTRADORES 
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('courts', CourtController::class);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // RUTAS QUE DESGLOSAN LA INFORMACION EN EL DASHBOARD DEL ADMIN
    Route::get('dashboard/week-detail',  [DashboardController::class, 'weekDetail'])->name('dashboard.week-detail');
    Route::get('dashboard/month-detail', [DashboardController::class, 'monthDetail'])->name('dashboard.month-detail');
});


// RUTAS SOLO PARA ENTRENADOR -> ACCESIBLE SOLO PARA ENTRENADORES Y ADMIN
Route::middleware(['auth', 'role:coach,admin'])->prefix('coach')->name('coach.')->group(function () {
    Route::resource('classes', ClassController::class)->except(['show']);
});


// RUTAS SOLO PARA USUARIO/JUGADOR -> ACCESIBLE SOLO PARA JUGADORES Y ADMIN
Route::middleware(['auth', 'role:player,admin'])->prefix('player')->name('player.')->group(function () {
    Route::resource('reservations', ReservationController::class)
        ->only(['index', 'create', 'store', 'destroy']);
});


require __DIR__ . '/auth.php';
