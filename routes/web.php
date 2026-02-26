<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // colocations management
    Route::resource('colocations', \App\Http\Controllers\ColocationController::class);

    // membership actions
    Route::post('colocations/{colocation}/join', [\App\Http\Controllers\ColocationController::class, 'join'])
        ->name('colocations.join');
    Route::post('colocations/{colocation}/leave', [\App\Http\Controllers\ColocationController::class, 'leave'])
        ->name('colocations.leave');
    Route::post('colocations/{colocation}/cancel', [\App\Http\Controllers\ColocationController::class, 'cancel'])
        ->name('colocations.cancel');
});

require __DIR__.'/auth.php';
