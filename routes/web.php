<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('auth.login');
})->name('home');

/*
 * Toda rota autenticada passa por 'force.password': enquanto a troca
 * obrigatória de senha estiver pendente, o usuário só consegue sair,
 * trocar a senha e nada mais.
 */
Route::middleware(['auth', 'force.password'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/force-password-change', [AuthController::class, 'showForcePasswordChange'])
        ->name('force-password-change');
    Route::post('/force-password-change', [AuthController::class, 'forcePasswordChange'])
        ->name('force-password-change.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('check.role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::get('audits', [AuditController::class, 'index'])->name('audits.index');

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    /*
     * Relatórios seguem a mesma divisão de papéis das listagens: quem
     * não abre a área de saúde também não a exporta.
     */
    Route::middleware('check.role:admin,medico,tecnico')->group(function () {
        Route::get('relatorios', [ReportController::class, 'index'])->name('reports.index');
        Route::get('relatorios/{relatorio}/pdf', [ReportController::class, 'exportarPdf'])->name('reports.pdf');
        Route::get('relatorios/{relatorio}/excel', [ReportController::class, 'exportarExcel'])->name('reports.excel');
        Route::get('relatorios/{relatorio}', [ReportController::class, 'show'])->name('reports.show');
    });

    Route::middleware('check.role:admin,medico')->group(function () {
        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/restore', [ExamController::class, 'restore'])->name('exams.restore');

        Route::resource('health', HealthController::class);
        Route::post('health/{health}/restore', [HealthController::class, 'restore'])->name('health.restore');
    });

    Route::middleware('check.role:admin,tecnico')->group(function () {
        Route::resource('risks', RiskController::class);
        Route::post('risks/{risk}/restore', [RiskController::class, 'restore'])->name('risks.restore');

        Route::resource('incidents', IncidentController::class);
        Route::post('incidents/{incident}/restore', [IncidentController::class, 'restore'])->name('incidents.restore');
    });
});

require __DIR__.'/auth.php';
