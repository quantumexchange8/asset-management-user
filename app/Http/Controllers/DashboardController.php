<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){

        $user = Auth::user();

        $user_wallet_id = $user->wallets->pluck('id');

        $wallet_transaction_history = Transaction::with([
            'from_wallet.user',
            'to_wallet.user',
        ])->where(function ($query) use ($user_wallet_id) {
            $query->whereIn('from_wallet_id', $user_wallet_id)
                ->orWhereIn('to_wallet_id', $user_wallet_id);
        })
        ->where('status', 'success')
        ->orderBy('approval_at', 'desc')
        ->get();

        $user_wallet_count = $user->wallets->count();

        return Inertia::render('Dashboard', [
            'user_wallet_count' => $user_wallet_count,
            'wallet_transaction_history' => $wallet_transaction_history,
        ]);
    }

    public function getWalletData(){
        $user = Auth::user();

        $user_wallets = $user->wallets;

        return response()->json([
            'user_wallets' => $user_wallets,
        ]);
    }
}
