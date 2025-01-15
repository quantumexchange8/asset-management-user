<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function getWalletHistory()
    {
        $user = Auth::user();
        $user_wallet_count = $user->wallets->count();
        return Inertia::render('Wallet/WalletHistory', [
            'user_wallet_count' => $user_wallet_count,
        ]);
    }

    public function getWalletHistoryData(Request $request)
    {

        $user = Auth::user();
        $user_wallet_id = $user->wallets->pluck('id');

        $wallet_transaction_history = Transaction::with([
            'from_wallet.user',
            'to_wallet.user',
        ])->where(function ($query) use ($user_wallet_id) {
            $query->whereIn('from_wallet_id', $user_wallet_id)
                ->orWhereIn('to_wallet_id', $user_wallet_id);
        })
        ->orderBy('approval_at', 'desc')
        ->get();


        return response()->json([
            'wallet_transaction_history' => $wallet_transaction_history,
        ]);
    }
}
