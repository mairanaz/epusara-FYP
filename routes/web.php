<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserDependentController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth'])->name('dashboard');

// USER routes
Route::middleware(['auth'])->group(function () {

    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    // USER PROFILE (Maklumat Diri)
    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('user.profile.show');
    Route::get('/user/profile/create', [UserProfileController::class, 'create'])->name('user.profile.create');
    Route::post('/user/profile', [UserProfileController::class, 'store'])->name('user.profile.store');
    Route::get('/user/profile/edit', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');

    // USER DEPENDENTS / TANGGUNGAN
    Route::resource('/user/dependents', UserDependentController::class)
        ->names('user.dependents');

    Route::get('/user/payments', [PaymentController::class, 'index'])->name('user.payments.index');
    Route::get('/user/payments/create', [PaymentController::class, 'create'])->name('user.payments.create');
    Route::post('/user/payments', [PaymentController::class, 'store'])->name('user.payments.store');

    // Breeze profile setting (default) - KEEP ONE ONLY
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN routes
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__ . '/auth.php';