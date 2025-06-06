<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/users/email/{email}', [UserController::class, 'getByEmail'])->name('users.email');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'user' => Auth::user(),
    ]);
});

Route::middleware([
    'auth:web', // Changed to 'web' guard explicitly
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Client Routes
    Route::resource('clients', ClientController::class);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/export/123', [ClientController::class, 'exportAll'])->name('clients.export');
    Route::get('/clients/{id}/export', [ClientController::class, 'exportClient'])->name('clients.export.single');
    Route::get('/clients/{id}/export-filtered', [ClientController::class, 'exportClientWithDateFilter'])->name('clients.export.filtered');
    Route::post('/clients/{id}/add-balance', [ClientController::class, 'addBalance'])->name('clients.addBalance');

    // Role Routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('roles/{role}/permissions/assign', [RoleController::class, 'assignPermission'])->name('roles.permissions.assign')->middleware(['auth']);
    Route::post('roles/{role}/permissions/revoke', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke')->middleware(['auth']);
    Route::post('users/{user}/roles/assign', [RoleController::class, 'assignRoleToUser'])->name('users.roles.assign')->middleware(['auth']);
    Route::post('users/{user}/roles/revoke', [RoleController::class, 'revokeRoleFromUser'])->name('users.roles.revoke')->middleware(['auth']);

    // User Routes
    Route::resource('users', UserController::class);
});