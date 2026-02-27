<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\SettlementController;
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

    // Colocations management
    Route::resource('colocations', ColocationController::class);
    Route::post('colocations/{colocation}/join', [ColocationController::class, 'join'])->name('colocations.join');
    Route::post('colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::post('colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('colocations/{colocation}/remove-member', [ColocationController::class, 'removeMember'])->name('colocations.remove-member');

    // Expenses management
    Route::get('colocations/{colocation}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('colocations/{colocation}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('colocations/{colocation}/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Categories management
    Route::get('colocations/{colocation}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('colocations/{colocation}/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('colocations/{colocation}/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('colocations/{colocation}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Invitations management
    Route::get('colocations/{colocation}/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('colocations/{colocation}/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('colocations/{colocation}/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('colocations/{colocation}/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

    // Invitation acceptance/decline (public routes with auth)
    Route::get('invitations/respond', [InvitationController::class, 'respondForm'])->name('invitations.respond');
    Route::post('invitations/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('invitations/decline', [InvitationController::class, 'decline'])->name('invitations.decline');

    // Settlements
    Route::get('colocations/{colocation}/settlements', [SettlementController::class, 'index'])->name('settlements.index');
    Route::post('colocations/{colocation}/settlements/mark-paid', [SettlementController::class, 'markPaid'])->name('settlements.mark-paid');
});

require __DIR__.'/auth.php';

