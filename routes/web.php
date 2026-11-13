<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('home');

    Route::resource('entrate-lavoro', IncomeController::class)
        ->parameters(['entrate-lavoro' => 'income'])
        ->except('show')
        ->names('incomes');

    Route::get('/partita-iva', [BusinessController::class, 'show'])->name('business');
    Route::put('/partita-iva', [BusinessController::class, 'update'])->name('business.update');

    Route::resource('movimenti', MovementController::class)
        ->parameters(['movimenti' => 'movement'])
        ->except(['show', 'create'])
        ->names('movements');

    Route::get('/famiglia', [FamilyController::class, 'show'])->name('family');
    Route::put('/famiglia/entrate', [FamilyController::class, 'updateIncome'])->name('family.income');
    Route::put('/famiglia/spese', [FamilyController::class, 'updateExpenses'])->name('family.expenses.update');
    Route::post('/famiglia/spese', [FamilyController::class, 'storeExpense'])->name('family.expenses.store');
    Route::delete('/famiglia/spese/{expense}', [FamilyController::class, 'destroyExpense'])->name('family.expenses.destroy');

    Route::resource('scadenze', DeadlineController::class)
        ->parameters(['scadenze' => 'deadline'])
        ->except(['show', 'create'])
        ->names('deadlines');
    Route::patch('/scadenze/{deadline}/pagata', [DeadlineController::class, 'togglePaid'])->name('deadlines.toggle');

    Route::get('/impostazioni', [SettingsController::class, 'show'])->name('settings');
    Route::get('/impostazioni/backup', [SettingsController::class, 'export'])->name('settings.export');
    Route::post('/impostazioni/backup', [SettingsController::class, 'import'])->name('settings.import');
    Route::post('/impostazioni/ripristina', [SettingsController::class, 'reset'])->name('settings.reset');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
