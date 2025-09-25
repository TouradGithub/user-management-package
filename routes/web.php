<?php

use Illuminate\Support\Facades\Route;
use Tourad\UserManager\Http\Controllers\Auth\LoginController;
use Tourad\UserManager\Http\Controllers\DashboardController;
use Tourad\UserManager\Http\Controllers\UserController;
use Tourad\UserManager\Http\Controllers\UserTypeController;
use Tourad\UserManager\Http\Controllers\ActivityController;
use Tourad\UserManager\Http\Controllers\SessionController;
use Tourad\UserManager\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| User Manager Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::name('user-manager.')->group(function () {
    
    // Guest Routes (Login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        
        // Dashboard Routes
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Users Management
        Route::resource('users', UserController::class);
        Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

        // User Types Management
        Route::resource('user-types', UserTypeController::class)->except(['show']);
        Route::post('/user-types/{userType}/toggle', [UserTypeController::class, 'toggle'])->name('user-types.toggle');

        // Activities
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities');
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        Route::post('/activities/bulk-delete', [ActivityController::class, 'bulkDelete'])->name('activities.bulk-delete');

        // Sessions Management
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions');
        Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
        Route::post('/sessions/bulk-terminate', [SessionController::class, 'bulkTerminate'])->name('sessions.bulk-terminate');

        // Profile Management
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/terminate-session', [ProfileController::class, 'terminateSession'])->name('profile.terminate-session');

        // Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    });
    
    // Redirect root to dashboard if authenticated, otherwise to login
    Route::get('/', function () {
        return auth()->check() ? redirect()->route('user-manager.dashboard') : redirect()->route('user-manager.login');
    })->name('home');
});