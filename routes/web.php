<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccidentController;

Route::get('/', [AccidentController::class, 'create'])->name('accidents.create');

Route::post('/accident/enregistrement', [AccidentController::class, 'store'])->name('accidents.store');