<?php

use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\Web\ComplaintWebController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\PortalDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::get('/tracking/{ticket}', [ComplaintWebController::class, 'track'])->name('tracking.show');

Route::middleware('auth')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [PortalDashboardController::class, 'index'])->name('dashboard.alias');
    Route::get('/complaints', [ComplaintWebController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintWebController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintWebController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintWebController::class, 'show'])->name('complaints.show');
});
