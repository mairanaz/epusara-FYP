<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserDependentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\DeathReportController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminDeathReportController;
use App\Http\Controllers\Admin\AdminKhairatMemberController;
use App\Http\Controllers\Admin\AdminKhairatDependentController;
use App\Http\Controllers\Admin\AdminKhairatFeeController;
use App\Http\Controllers\Admin\AdminBurialMapController;
use App\Http\Controllers\PaymentGatewayController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/whatsapp/lapor-kematian', [WhatsAppController::class, 'laporKematian'])
    ->name('whatsapp.lapor-kematian');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->account_type === null) {
        return redirect()->route('user.profile.create.step1')
            ->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
    }

    if ($user->account_type === 'tanggungan') {
        return redirect()->route('dependent.dashboard');
    }

    return redirect()->route('user.dashboard');
})->middleware(['auth'])->name('dashboard');

// USER routes
Route::middleware(['auth'])->group(function () {

    Route::get('/user/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->account_type === null) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
        }

        if ($user->account_type === 'tanggungan') {
            return redirect()->route('dependent.dashboard');
        }

        return view('user.dashboard');
    })->name('user.dashboard');

    Route::get('/dependent/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->account_type === null) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
        }

        if ($user->account_type !== 'tanggungan') {
            return redirect()->route('user.dashboard');
        }

        return view('dependent.dashboard');
    })->name('dependent.dashboard');

    Route::get('/dependent/main-member', [UserProfileController::class, 'dependentMainMember'])
        ->name('dependent.main-member');

    // USER PROFILE (Maklumat Diri) - MULTI STEP
    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('user.profile.show');

    Route::prefix('user/profile/create')->name('user.profile.')->group(function () {
        Route::get('/step1', [UserProfileController::class, 'createStep1'])->name('create.step1');
        Route::post('/step1', [UserProfileController::class, 'postStep1'])->name('post.step1');

        Route::get('/step2', [UserProfileController::class, 'createStep2'])->name('create.step2');
        Route::post('/step2', [UserProfileController::class, 'postStep2'])->name('post.step2');

        Route::get('/step3', [UserProfileController::class, 'createStep3'])->name('create.step3');
        Route::post('/step3', [UserProfileController::class, 'postStep3'])->name('post.step3');

        Route::get('/step4', [UserProfileController::class, 'createStep4'])->name('create.step4');
        Route::post('/step4', [UserProfileController::class, 'storeFinal'])->name('store.final');
    });

    Route::get('/user/profile/edit', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');

    // USER DEPENDENTS / TANGGUNGAN
    Route::resource('/user/dependents', UserDependentController::class)
        ->names('user.dependents');

    // USER PAYMENTS
    Route::get('/user/payments', [PaymentController::class, 'index'])->name('user.payments.index');
    Route::get('/user/payments/create', [PaymentController::class, 'create'])->name('user.payments.create');
    Route::post('/user/payments', [PaymentController::class, 'store'])->name('user.payments.store');
    Route::get('/user/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('user.payments.receipt');

    // BILLPLZ PAYMENT GATEWAY
    Route::get('/user/payments/{payment}/billplz', [PaymentGatewayController::class, 'createBill'])->name('payment.billplz');

    //USER DEATH-REPORTS
    Route::get('/lapor-kematian', [DeathReportController::class, 'create'])->name('death-reports.create');
    Route::post('/lapor-kematian', [DeathReportController::class, 'store'])->name('death-reports.store');
    Route::get('/status-laporan-kematian', [DeathReportController::class, 'index'])->name('death-reports.index');
    Route::get('/status-laporan-kematian/{deathReport}', [DeathReportController::class, 'show'])->name('death-reports.show');

    // Breeze profile setting
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// BILLPLZ RETURN & CALLBACK
Route::get('/payment/return', [PaymentGatewayController::class, 'paymentReturn'])->name('payment.return');
Route::post('/payment/callback', [PaymentGatewayController::class, 'paymentCallback'])->name('payment.callback');

// ADMIN routes
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Pengurusan Khairat
    |--------------------------------------------------------------------------
    */
    Route::prefix('khairat')->name('khairat.')->group(function () {
        Route::get('/members', [AdminKhairatMemberController::class, 'index'])->name('members.index');
        Route::get('/members/{member}', [AdminKhairatMemberController::class, 'show'])->name('members.show');
        Route::get('/dependents', [AdminKhairatDependentController::class, 'index'])->name('dependents.index');
        Route::get('/fees', [AdminKhairatFeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/{payment}', [AdminKhairatFeeController::class, 'show'])->name('fees.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Permohonan Keahlian
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/{profile}', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/{profile}/approve', [AdminProfileController::class, 'approve'])->name('profile.approve');
    Route::post('/profile/{profile}/reject', [AdminProfileController::class, 'reject'])->name('profile.reject');

    /*
    |--------------------------------------------------------------------------
    | Laporan Kematian
    |--------------------------------------------------------------------------
    */
    Route::get('/death-reports', [AdminDeathReportController::class, 'index'])->name('death-reports.index');
    Route::get('/death-reports/{deathReport}', [AdminDeathReportController::class, 'show'])->name('death-reports.show');
    Route::post('/death-reports/{deathReport}/verify', [AdminDeathReportController::class, 'verify'])->name('death-reports.verify');
    Route::get('/death-reports/{deathReport}/preview/{type}', [AdminDeathReportController::class, 'preview'])->name('death-reports.preview');
    Route::get('/death-reports/{deathReport}/select-plot', [AdminDeathReportController::class, 'selectPlot'])->name('death-reports.select-plot');
    Route::post('/death-reports/{deathReport}/store-plot', [AdminDeathReportController::class, 'storePlot'])->name('death-reports.store-plot');

    //peta plot kubur
    Route::get('/burial-map', [AdminBurialMapController::class, 'index'])->name('burial-map.index');
});

require __DIR__ . '/auth.php';