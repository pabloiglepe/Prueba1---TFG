<?php

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Player\ReservationController;
use App\Http\Controllers\Coach\ClassController;

// LE HE DADO NOMBRE PARA DIFERENCIAR CON 'COACH'
use App\Http\Controllers\Player\ClassController as PlayerClassController;

use App\Http\Controllers\ProfileController as UserProfileController;
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


    // RUTAS PARA MANEJO DEL PERFIL
    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::patch('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('profile/export', [UserProfileController::class, 'export'])->name('profile.export');
});


// RUTAS SOLO PARA ADMIN -> ACCESIBLE SOLO PARA ADMINISTRADORES 
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('courts', CourtController::class);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->except(['show']);

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
    Route::get('classes', [PlayerClassController::class, 'index'])->name('classes.index');
    Route::post('classes/{class}/register', [PlayerClassController::class, 'register'])->name('classes.register');
    Route::post('classes/{class}/cancel', [PlayerClassController::class, 'cancel'])->name('classes.cancel');
});


require __DIR__ . '/auth.php';
