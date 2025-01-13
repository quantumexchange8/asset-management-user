<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){

        $user = Auth::user();
        $user_wallet_count = $user->wallets->count();
        return Inertia::render('Dashboard', [
            'user_wallet_count' => $user_wallet_count,
        ]);
    }

    public function getWalletData(){
        $user_wallets = Auth::user();

        return response()->json($user_wallets->wallets);
    }
}
