<?php

namespace App\Http\Controllers;

use App\Models\DepositProfile;
use App\Models\PaymentAccount;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
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

        if ($wallet->balance < $request->amount) {
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
        $transaction->to_bank_sub_branch = $paymentAccount->bank_sub_branch ?? null;
        $transaction->to_bank_branch_address = $paymentAccount->bank_branch_address ?? null;
        $transaction->amount = $request->amount;
        $transaction->transaction_charges = $request->transaction_charges;
        $transaction->from_currency = 'USD';
        $transaction->to_currency = $paymentAccount->currency;
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

    public function withdrawalHistory()
    {

        return Inertia::render('WalletHistory/WithdrawalHistory');
    }

    public function getWithdrawalHistoryData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true); //only() extract parameters in lazyEvent

            //user query
            $query = Transaction::query()
                ->with([
                    'user:id,name,email,upline_id',
                    'user.upline:id,name,email',
                    'from_wallet:id,type,address,currency_symbol',
                    'to_wallet:id,type,address,currency_symbol',
                ])
                ->where('transaction_type', 'withdrawal')
                ->where('user_id', Auth::id())
                ->whereNot('status', 'processing');


            //global filter
            if (!empty($data['filters']['global']['value'])) {
                $query->whereHas('user', function ($q) use ($data) {
                    $keyword = $data['filters']['global']['value'];

                    // Filter on the 'name' column in the related 'user' table
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('transaction_number', 'like', '%' . $keyword . '%');
                });
            }

            //date filter
            if (!empty($data['filters']['start_date']['value']) && !empty($data['filters']['end_date']['value'])) {
                $start_date = Carbon::parse($data['filters']['start_date']['value'])->addDay()->startOfDay(); //add day to ensure capture entire day
                $end_date = Carbon::parse($data['filters']['end_date']['value'])->addDay()->endOfDay();

                $query->whereBetween('approval_at', [$start_date, $end_date]);
            }

            //status filter
            if ($data['filters']['status']['value']) {
                $query->where('status', $data['filters']['status']['value']);
            }

            //sort field/order
            if ($data['sortField'] && $data['sortOrder']) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->latest();
            }

            $users = $query->paginate($data['rows']);

            $successAmount = (clone $query)
                ->where('status', 'success')
                ->sum('transaction_amount');

            $rejectAmount = (clone $query)
                ->where('status', 'rejected')
                ->sum('transaction_amount');

            $withdrawalHistoryCounts = (clone $query)
                ->count();

            return response()->json([
                'success' => true,
                'data' => $users,
                'successAmount' => $successAmount,
                'rejectAmount' => $rejectAmount,
                'withdrawalHistoryCounts' => $withdrawalHistoryCounts,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }
}
