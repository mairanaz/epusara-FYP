<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserDependentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\DeathReportController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\GraveOrderController;
use App\Http\Controllers\UserGraveLocationController;
use App\Http\Controllers\UserUpgradeMembershipController;
use App\Http\Controllers\PublicGraveSearchController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Auth\GoogleAuthController;

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminDeathReportController;
use App\Http\Controllers\Admin\AdminKhairatMemberController;
use App\Http\Controllers\Admin\AdminKhairatDependentController;
use App\Http\Controllers\Admin\AdminKhairatFeeController;
use App\Http\Controllers\Admin\AdminBurialMapController;
use App\Http\Controllers\Admin\AdminGraveOrderController;
use App\Http\Controllers\Admin\GraveOrderReportController;
use App\Http\Controllers\Admin\AdminBurialRecordController;
use App\Http\Controllers\Admin\AdminDeathReportReportController;
use App\Http\Controllers\Admin\MainMemberSuccessorController;
use App\Http\Controllers\Admin\AdminMemberReportController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

Route::get('/whatsapp/lapor-kematian', [WhatsAppController::class, 'laporKematian'])
    ->name('whatsapp.lapor-kematian');

Route::get('/ziarah-kubur', [PublicGraveSearchController::class, 'index'])
    ->name('public.grave-search.index');

Route::get('/ziarah-kubur/{deathReport}', [PublicGraveSearchController::class, 'show'])
    ->name('public.grave-search.show');

/*
|--------------------------------------------------------------------------
| MAIN DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/

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



/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | NAIK TARAF AKAUN TANGGUNGAN KE AHLI UTAMA
    |--------------------------------------------------------------------------
    | Route ini mesti di luar middleware check.dependent.eligibility.
    | Sebab akaun tanggungan yang tidak layak masih perlu akses page ini.
    |--------------------------------------------------------------------------
    */

    Route::get('/user/upgrade-membership', [UserUpgradeMembershipController::class, 'create'])
        ->name('user.upgrade-membership.create');

    Route::post('/user/upgrade-membership', [UserUpgradeMembershipController::class, 'store'])
        ->name('user.upgrade-membership.store');

    /*
    |--------------------------------------------------------------------------
    | USER ROUTES YANG PERLU DISEMAK KELAYAKAN TANGGUNGAN
    |--------------------------------------------------------------------------
    | Kalau user ialah tanggungan dan status_tanggungan = tidak_layak,
    | middleware akan redirect ke page naik taraf ahli utama.
    |--------------------------------------------------------------------------
    */

    Route::middleware(['check.dependent.eligibility'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | USER DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get('/user/dashboard', [DashboardController::class, 'index'])
        ->name('user.dashboard');

        /*
        |--------------------------------------------------------------------------
        | DEPENDENT DASHBOARD
        |--------------------------------------------------------------------------
        */

            Route::get('/dependent/dashboard', [DashboardController::class, 'dependentDashboard'])
        ->name('dependent.dashboard');

        Route::get('/dependent/main-member', [UserProfileController::class, 'dependentMainMember'])
            ->name('dependent.main-member');

        /*
        |--------------------------------------------------------------------------
        | USER PROFILE - MULTI STEP REGISTRATION
        |--------------------------------------------------------------------------
        */

        Route::get('/user/profile', [UserProfileController::class, 'show'])
            ->name('user.profile.show');

        Route::prefix('user/profile/create')->name('user.profile.')->group(function () {
            Route::get('/step1', [UserProfileController::class, 'createStep1'])
                ->name('create.step1');

            Route::post('/step1', [UserProfileController::class, 'postStep1'])
                ->name('post.step1');

            Route::get('/step2', [UserProfileController::class, 'createStep2'])
                ->name('create.step2');

            Route::post('/step2', [UserProfileController::class, 'postStep2'])
                ->name('post.step2');

           /* Route::get('/step3', [UserProfileController::class, 'createStep3'])
                ->name('create.step3');

            Route::post('/step3', [UserProfileController::class, 'postStep3'])
                ->name('post.step3'); */

            Route::get('/step3', [UserProfileController::class, 'createStep4'])
                ->name('create.step4');

            Route::post('/step3', [UserProfileController::class, 'storeFinal'])
                ->name('store.final');
        });

        Route::get('/user/profile/edit', [UserProfileController::class, 'edit'])
            ->name('user.profile.edit');

        Route::put('/user/profile', [UserProfileController::class, 'update'])
            ->name('user.profile.update');

        /*
        |--------------------------------------------------------------------------
        | USER DEPENDENTS / TANGGUNGAN
        |--------------------------------------------------------------------------
        */

        Route::resource('/user/dependents', UserDependentController::class)
            ->names('user.dependents');

        /*
        |--------------------------------------------------------------------------
        | USER PAYMENTS
        |--------------------------------------------------------------------------
        */

        Route::get('/user/payments', [PaymentController::class, 'index'])
            ->name('user.payments.index');

        Route::get('/user/payments/create', [PaymentController::class, 'create'])
            ->name('user.payments.create');

        Route::post('/user/payments', [PaymentController::class, 'store'])
            ->name('user.payments.store');

        Route::get('/user/payments/{payment}/receipt', [PaymentController::class, 'receipt'])
            ->name('user.payments.receipt');

        /*
        |--------------------------------------------------------------------------
        | BILLPLZ PAYMENT GATEWAY
        |--------------------------------------------------------------------------
        */

        Route::get('/user/payments/{payment}/billplz', [PaymentGatewayController::class, 'createBill'])
            ->name('payment.billplz');

        /*
        |--------------------------------------------------------------------------
        | USER DEATH REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get('/lapor-kematian', [DeathReportController::class, 'create'])
            ->name('death-reports.create');

        Route::post('/lapor-kematian', [DeathReportController::class, 'store'])
            ->name('death-reports.store');

        Route::get('/status-laporan-kematian', [DeathReportController::class, 'index'])
            ->name('death-reports.index');

        Route::get('/status-laporan-kematian/{deathReport}', [DeathReportController::class, 'show'])
            ->name('death-reports.show');

        /*
        |--------------------------------------------------------------------------
        | USER TEMPAHAN KEPUK / NISAN
        |--------------------------------------------------------------------------
        */

        Route::get('/tempahan-kepuk', [GraveOrderController::class, 'index'])
            ->name('grave-orders.index');

        Route::get('/tempahan-kepuk/create', [GraveOrderController::class, 'create'])
            ->name('grave-orders.create');

        Route::post('/tempahan-kepuk', [GraveOrderController::class, 'store'])
            ->name('grave-orders.store');

        Route::get('/tempahan-kepuk/{graveOrder}', [GraveOrderController::class, 'show'])
            ->name('grave-orders.show');

        /*
        |--------------------------------------------------------------------------
        | USER LOKASI PETA KUBUR
        |--------------------------------------------------------------------------
        */

        Route::get('/grave-locations', [UserGraveLocationController::class, 'index'])
            ->name('user.grave-locations.index');

        Route::get('/grave-locations/{deathReport}', [UserGraveLocationController::class, 'show'])
            ->name('user.grave-locations.show');

        /*
        |--------------------------------------------------------------------------
        | BREEZE PROFILE SETTING
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| BILLPLZ RETURN & CALLBACK
|--------------------------------------------------------------------------
*/

Route::get('/payment/return', [PaymentGatewayController::class, 'paymentReturn'])
    ->name('payment.return');

Route::post('/payment/callback', [PaymentGatewayController::class, 'paymentCallback'])
    ->name('payment.callback');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PENGURUSAN KHAIRAT
    |--------------------------------------------------------------------------
    */

    Route::prefix('khairat')->name('khairat.')->group(function () {
        Route::get('/members', [AdminKhairatMemberController::class, 'index'])
            ->name('members.index');

        Route::get('/members/{member}', [AdminKhairatMemberController::class, 'show'])
            ->name('members.show');

        Route::get('/dependents', [AdminKhairatDependentController::class, 'index'])
            ->name('dependents.index');

        Route::get('/fees', [AdminKhairatFeeController::class, 'index'])
            ->name('fees.index');

        Route::get('/fees/export/excel', [AdminKhairatFeeController::class, 'exportExcel'])
            ->name('fees.export.excel');

        Route::get('/fees/export/pdf', [AdminKhairatFeeController::class, 'previewPdf'])
            ->name('fees.export.pdf');

        Route::get('/fees/{payment}', [AdminKhairatFeeController::class, 'show'])
            ->name('fees.show');

    });

    /*
    |--------------------------------------------------------------------------
    | PENGGANTI AHLI UTAMA
    |--------------------------------------------------------------------------
    */

    Route::get('/members/{user}/successor', [MainMemberSuccessorController::class, 'show'])
        ->name('members.successor.show');

    Route::post('/members/{user}/successor', [MainMemberSuccessorController::class, 'store'])
        ->name('members.successor.store');

    /*
    |--------------------------------------------------------------------------
    | PERMOHONAN KEAHLIAN
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [AdminProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/{profile}', [AdminProfileController::class, 'show'])
        ->name('profile.show');

    Route::post('/profile/{profile}/approve', [AdminProfileController::class, 'approve'])
        ->name('profile.approve');

    Route::post('/profile/{profile}/reject', [AdminProfileController::class, 'reject'])
        ->name('profile.reject');

    /*
    |--------------------------------------------------------------------------
    | PENGURUSAN KEMATIAN
    |--------------------------------------------------------------------------
    */

    Route::get('/death-reports', [AdminDeathReportController::class, 'index'])
        ->name('death-reports.index');

    Route::get('/death-reports/{deathReport}', [AdminDeathReportController::class, 'show'])
        ->name('death-reports.show');

    Route::post('/death-reports/{deathReport}/verify', [AdminDeathReportController::class, 'verify'])
        ->name('death-reports.verify');

    Route::get('/death-reports/{deathReport}/preview/{type}', [AdminDeathReportController::class, 'preview'])
        ->name('death-reports.preview');

    Route::get('/death-reports/{deathReport}/select-plot', [AdminDeathReportController::class, 'selectPlot'])
        ->name('death-reports.select-plot');

    Route::post('/death-reports/{deathReport}/store-plot', [AdminDeathReportController::class, 'storePlot'])
        ->name('death-reports.store-plot');

    /*
    |--------------------------------------------------------------------------
    | PETA PLOT KUBUR
    |--------------------------------------------------------------------------
    */

    Route::get('/burial-map', [AdminBurialMapController::class, 'index'])
        ->name('burial-map.index');

    /*
    |--------------------------------------------------------------------------
    | TEMPAHAN KEPUK DAN NISAN
    |--------------------------------------------------------------------------
    */

    Route::get('/grave-orders/export/excel', [AdminGraveOrderController::class, 'exportExcel'])
        ->name('grave-orders.export.excel');

    Route::get('/grave-orders/export/pdf', [AdminGraveOrderController::class, 'exportPdf'])
        ->name('grave-orders.export.pdf');

    Route::get('/grave-orders', [AdminGraveOrderController::class, 'index'])
        ->name('grave-orders.index');

    Route::get('/grave-orders/{graveOrder}', [AdminGraveOrderController::class, 'show'])
        ->name('grave-orders.show');

    Route::put('/grave-orders/{graveOrder}', [AdminGraveOrderController::class, 'update'])
        ->name('grave-orders.update');

    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */
    Route::get('/reports/members', [AdminMemberReportController::class, 'index'])
    ->name('reports.members.index');

    Route::get('/reports/grave-orders', [GraveOrderReportController::class, 'index'])
        ->name('reports.grave-orders.index');

    Route::get('/reports/deaths', [AdminDeathReportReportController::class, 'index'])
            ->name('reports.deaths.index');
    /*
    |--------------------------------------------------------------------------
    | REKOD KUBUR
    |--------------------------------------------------------------------------
    */

    Route::get('/burial-records', [AdminBurialRecordController::class, 'index'])
        ->name('burial-records.index');

    Route::get('/burial-records/{deathReport}', [AdminBurialRecordController::class, 'show'])
        ->name('burial-records.show');

    Route::post('/burial-records/{deathReport}/grave-image', [AdminBurialRecordController::class, 'updateGraveImage'])
        ->name('burial-records.update-grave-image');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';