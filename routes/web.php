<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TechWorkspaceController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/client/settings/avatar', [ClientController::class, 'updateAvatar'])->name('client.settings.avatar');

Route::middleware('auth')->group(function () {
    Route::prefix('tech')->name('tech.')->group(function () {
        Route::get('/workspace', [TechWorkspaceController::class, 'index'])->name('workspace');
        Route::post('/tickets/{ticket}/claim', [TechWorkspaceController::class, 'claim'])->name('tickets.claim');
        Route::get('/tickets/{ticket}/resolve', [TechWorkspaceController::class, 'showResolve'])->name('tickets.resolve.form');
        Route::post('/tickets/{ticket}/resolve', [TechWorkspaceController::class, 'resolve'])->name('tickets.resolve');
    });
    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::post('/client/tickets', [ClientController::class, 'storeTicket'])->name('client.tickets.store');
    Route::get('/client/assets', [ClientController::class, 'assets'])->name('client.assets');
    Route::get('/client/settings', [ClientController::class, 'settings'])->name('client.settings');
    Route::post('/client/settings/password', [ClientController::class, 'updatePassword'])->name('client.settings.password');
});
