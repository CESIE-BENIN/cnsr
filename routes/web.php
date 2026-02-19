<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccidentController;
use App\Http\Controllers\FormulaireController;
use App\Http\Controllers\Admin\AdminDashboardController;
Route::get('/', function () {
    return view('auth/login');
});

Route::middleware('auth')->group(function () {

    Route::get('/formulaire', [AccidentController::class, 'create'])
    ->name('formulaire');

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});


    Route::post('/accidents', [AccidentController::class, 'store'])
        ->name('accidents.store');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
