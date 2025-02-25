<?php

namespace App\Http\Controllers;

use App\Models\DepositProfile;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

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

    public function deposit(Request $request)
    {
        Validator::make($request->all(), [
            'wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'min:50'],
            'payment_slips' => ['required'],
        ])->setAttributeNames([
            'wallet_id' => trans('public.wallet'),
            'amount' => trans('public.amount'),
            'payment_slips' => trans('public.upload_payment_slip'),
        ])->validate();

        $wallet = Wallet::find($request->wallet_id);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category' => 'cash_wallet',
            'transaction_type' => 'deposit',
            'to_wallet_id' => $wallet->id,
            'transaction_number' => RunningNumberService::getID('transaction'),
            'to_payment_platform' => 'crypto',
            'txn_hash' => $request->txn_hash,
            'amount' => $request->amount,
            'from_currency' => 'USD',
            'to_currency' => 'USD',
            'transaction_amount' => $request->amount,
            'fund_type' => 'real_fund',
            'old_wallet_amount' => $wallet->balance,
            'status' => 'processing'
        ]);

        if ($request->hasFile('payment_slips')) {
            foreach ($request->file('payment_slips') as $file) {
                try {
                    $transaction->addMedia($file)->toMediaCollection('payment_slips');
                } catch (FileDoesNotExist|FileIsTooBig $e) {
                    Log::error($e);
                    return back();
                }
            }
        }

        return back()->with('toast', 'success');
    }
}
