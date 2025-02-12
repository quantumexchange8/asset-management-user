<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function getWallets()
    {
        $wallets = Wallet::with('user')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'wallets' => $wallets
        ]);
    }

    public function getWalletHistory()
    {
        $user = Auth::user();
        $user_wallet_count = $user->wallets->count();
        return Inertia::render('Wallet/WalletHistory', [
            'user_wallet_count' => $user_wallet_count,
        ]);
    }
}
