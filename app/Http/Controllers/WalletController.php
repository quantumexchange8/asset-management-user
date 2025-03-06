<?php

namespace App\Http\Controllers;

use App\Models\DepositProfile;
use App\Models\PaymentAccount;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
                } catch (FileDoesNotExist | FileIsTooBig $e) {
                    Log::error($e);
                    return back();
                }
            }
        }

        return back()->with('toast', 'success');
    }

    public function withdrawal(Request $request)
    {
        Validator::make($request->all(), [
            'wallet_id' => ['required'],
            'payment_account_id' => ['required'],
            'amount' => ['required', 'min:100', 'numeric'],
        ])->setAttributeNames([
            'wallet_id' => trans('public.wallet'),
            'payment_account_id' => trans('public.payment_account'),
            'amount' => trans('public.amount'),
        ])->validate();

        $wallet = Wallet::find($request->wallet_id);

        if($wallet->balance < $request->amount){
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_wallet_balance')]);
        }

        $paymentAccount = PaymentAccount::find($request->payment_account_id);

        $receive_amount = $request->amount - $request->transaction_charges;
        $new_balance = $wallet->balance - $request->amount;

        $transaction = new Transaction();
        $transaction->user_id = Auth::id();
        $transaction->category = $wallet->type;
        $transaction->transaction_type = 'withdrawal';
        $transaction->from_wallet_id = $wallet->id;
        $transaction->transaction_number = RunningNumberService::getID('transaction');
        $transaction->to_payment_account_name = $paymentAccount->payment_account_name;
        $transaction->to_payment_platform = $paymentAccount->payment_platform;
        $transaction->to_payment_platform_name = $paymentAccount->payment_platform_name;
        $transaction->to_payment_account_no = $paymentAccount->account_no;
        $transaction->to_bank_sub_branch = $paymentAccount->bank_sub_branch;
        $transaction->to_bank_branch_address = $paymentAccount->bank_branch_address;
        $transaction->amount = $request->amount;
        $transaction->transaction_charges = $request->transaction_charges;
        $transaction->from_currency = 'USD';
        $transaction->to_currency = 'USD';
        $transaction->transaction_amount = $receive_amount;
        $transaction->fund_type = 'real_fund';
        $transaction->old_wallet_amount = $wallet->balance;
        $transaction->new_wallet_amount = $new_balance;
        $transaction->status = 'processing';
        $transaction->save();

        $wallet->balance -= $request->amount;
        $wallet->save();

        return back()->with('toast', 'success');
    }
}
