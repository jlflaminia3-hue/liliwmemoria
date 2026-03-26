<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
});

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/map', function () {
    $lots = \App\Models\Lot::with('deceased')->get();

    return view('map', compact('lots'));
})->name('public.map');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin/lots')->name('admin.lots.')->group(function () {
        Route::get('/', [LotController::class, 'index'])->name('index');
        Route::get('/create', [LotController::class, 'create'])->name('create');
        Route::post('/', [LotController::class, 'store'])->name('store');
        Route::get('/{lot}/edit', [LotController::class, 'edit'])->name('edit');
        Route::put('/{lot}', [LotController::class, 'update'])->name('update');
        Route::delete('/{lot}', [LotController::class, 'destroy'])->name('destroy');
        Route::get('/map', [LotController::class, 'map'])->name('map');
        Route::get('/next-lot-number', [LotController::class, 'nextLotNumber'])->name('nextLotNumber');
        Route::post('/with-deceased', [LotController::class, 'storeWithDeceased'])->name('storeWithDeceased');
    });
});

require __DIR__.'/auth.php';

Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');

// Route::post('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');
Route::middleware('guest')->post('/admin/register', [RegisteredUserController::class, 'store'])->name('admin.register');

Route::get('/verify', [AdminController::class, 'ShowVerification'])->name('custom.verification.form');

Route::post('/verify', [AdminController::class, 'VerificationVerify'])->name('custom.verification.verify');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');
