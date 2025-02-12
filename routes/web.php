<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SelectOptionController;
use App\Http\Controllers\WalletController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return Redirect::route('login');
});

//select option
Route::get('/get_countries', [SelectOptionController::class, 'getCountries'])->name('getCountries');


Route::middleware('auth')->group(function () {
    /**
     * ==============================
     *           Dashboard
     * ==============================
     */
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getWallets', [WalletController::class, 'getWallets'])->name('dashboard.getWallets');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //wallet
    Route::prefix('wallet')->group(function () {
        Route::get('/connections', [ReportController::class, 'connections'])->name('wallet.connections');
        Route::get('/get_wallet_history', [WalletController::class, 'getWalletHistory'])->name('wallet.getWalletHistory');
        Route::get('/get_wallet_history_data', [WalletController::class, 'getWalletHistoryData'])->name('wallet.getWalletHistoryData');

    });

    /**
     * ==============================
     *           Reports
     * ==============================
     */
    Route::prefix('report')->group(function () {
        Route::get('/lot_comission', [ReportController::class, 'lot_commission'])->name('report.lot_comission');
        Route::get('/fetchHelloWorld', [ReportController::class, 'fetchHelloWorld'])->name('report.fetchHelloWorld');
    });
});


require __DIR__ . '/auth.php';
