<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'force.password'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/force-password-change', [AuthController::class, 'forcePasswordChange'])
        ->name('force-password-change.update');
    Route::get('/force-password-change', [AuthController::class, 'showForcePasswordChange'])
        ->name('force-password-change');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('check.role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('check.role:admin,medico')->group(function () {
        Route::resource('exams', ExamController::class);
        Route::resource('health', HealthController::class);
    });

    Route::middleware('check.role:admin,tecnico')->group(function () {
        Route::resource('risks', RiskController::class);
        Route::resource('incidents', IncidentController::class);
    });
});

require __DIR__.'/auth.php';
