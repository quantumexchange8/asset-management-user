<?php

use App\Http\Controllers\BrokerController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SelectOptionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return Redirect::route('login');
});

// Change locale
Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);
    return redirect()->back();
});

// Admin login
Route::get('/admin_login/{hashedToken}', [DashboardController::class, 'admin_login']);

// Select option
Route::get('/get_countries', [SelectOptionController::class, 'getCountries'])->name('getCountries');

Route::middleware('auth')->group(function () {
    Route::get('/getDepositProfiles', [SelectOptionController::class, 'getDepositProfiles'])->name('getDepositProfiles');

    /**
     * ==============================
     *           Dashboard
     * ==============================
     */
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getWallets', [WalletController::class, 'getWallets'])->name('dashboard.getWallets');

        // Deposit
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
    });

    /**
     * ==============================
     *            Broker
     * ==============================
     */
    Route::prefix('broker')->group(function () {
        Route::get('/', [BrokerController::class, 'index'])->name('broker');
        Route::get('/getBrokerData', [BrokerController::class, 'getBrokerData'])->name('broker.getBrokerData');
    });

    /**
     * ==============================
     *          Connections
     * ==============================
     */
    Route::prefix('connections')->group(function () {
        Route::get('/', [ConnectionController::class, 'index'])->name('connections');
        Route::get('/getConnectionAccounts', [ConnectionController::class, 'getConnectionAccounts'])->name('connections.getConnectionAccounts');
        Route::get('/getConnectionsData', [ConnectionController::class, 'getConnectionsData'])->name('connections.getConnectionsData');
    });

    /**
     * ==============================
     *          Referrals
     * ==============================
     */
    Route::prefix('referral_programme')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('referral_programme');
        Route::get('/getReferralsData', [ReferralController::class, 'getReferralsData'])->name('referral_programme.getReferralsData');
        Route::get('/getDownlineData', [ReferralController::class, 'getDownlineData'])->name('referral_programme.getDownlineData');
    });

    /**
     * ==============================
     *           Reports
     * ==============================
     */
    Route::prefix('report')->group(function () {
        // Profit Report
        Route::get('/profit_sharing', [ReportController::class, 'profit_sharing'])->name('report.profit_sharing');
        Route::get('/getProfitSharingData', [ReportController::class, 'getProfitSharingData'])->name('report.getProfitSharingData');

        // Standard Bonus
        Route::get('/ib_group_incentive', [ReportController::class, 'ib_group_incentive'])->name('report.ib_group_incentive');
        Route::get('/getStandardBonusData', [ReportController::class, 'getStandardBonusData'])->name('report.getStandardBonusData');

        // Rebate Bonus
        Route::get('/rebate_bonus', [ReportController::class, 'rebate_bonus'])->name('report.rebate_bonus');
        Route::get('/getRebateBonusData', [ReportController::class, 'getRebateBonusData'])->name('report.getRebateBonusData');
    });

    /**
     * ==============================
     *           Profile
     * ==============================
     */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile');
        Route::post('/update', [ProfileController::class, 'update'])->name('profile.update'); // Fixed route
        Route::post('/uploadKyc', [ProfileController::class, 'uploadKyc'])->name('profile.uploadKyc');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
