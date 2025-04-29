<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;




Route::get('/users/email/{email}', [UserController::class, 'getByEmail'])->name('users.email');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'user' => Auth::user(),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/export', [ClientController::class, 'exportAll'])->name('clients.export');
    Route::get('/clients/{id}/export', [ClientController::class, 'exportClient'])->name('clients.export.single');
    Route::post('/clients/{id}/add-balance', [ClientController::class, 'addBalance'])->name('clients.addBalance');

    Route::resource('users', UserController::class);
});