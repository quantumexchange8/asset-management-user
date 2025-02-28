<?php

namespace App\Http\Controllers;

use App\Models\BrokerAccount;
use App\Models\BrokerConnection;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        $broker_accounts_count = BrokerAccount::where('user_id', Auth::id())
            ->count();

        return Inertia::render('Account/BrokerAccount', [
            'brokerAccountsCount' => $broker_accounts_count,
        ]);
    }

    public function getBrokerAccounts()
    {
        $broker_accounts = BrokerAccount::with([
            'broker',
            'broker.media'
        ])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'brokerAccounts' => $broker_accounts,
        ]);
    }

    public function getConnectionsData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);
            $broker_id = $data['filters']['broker_id'];
            $broker_login = $data['filters']['broker_login'];

            $query = BrokerConnection::with([
                'broker',
                'broker.media'
            ])
                ->where([
                    'user_id' => Auth::id(),
                    'broker_id' => $broker_id,
                    'broker_login' => $broker_login,
                ])
                ->whereIn('status', [
                    'active',
                    'removed'
                ]);

            if ($data['filters']['global']['value']) {
                $keyword = $data['filters']['global']['value'];

                $query->where(function ($q) use ($keyword) {
                    $q->where('broker_login', 'like', '%' . $keyword . '%')
                        ->orWhere('connection_number', 'like', '%' . $keyword . '%');
                });
            }

            if (!empty($data['filters']['start_join_date']['value']) && !empty($data['filters']['end_join_date']['value'])) {
                $start_join_date = \Illuminate\Support\Carbon::parse($data['filters']['start_join_date']['value'])->addDay()->startOfDay();
                $end_join_date = Carbon::parse($data['filters']['end_join_date']['value'])->addDay()->endOfDay();

                $query->whereBetween('joined_at', [$start_join_date, $end_join_date]);
            }

            if ($data['sortField'] && $data['sortOrder']) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->orderByDesc('joined_at');
            }

            $connections = $query->paginate($data['rows']);

            $totalActiveFund = (clone $query)
                ->sum('capital_fund');

            $totalConnections = (clone $query)
                ->distinct('user_id')
                ->count();

            return response()->json([
                'success' => true,
                'data' => $connections,
                'totalActiveFund' => $totalActiveFund,
                'totalConnections' => $totalConnections,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    public function linkAccount(Request $request)
    {
        Validator::make($request->all(), [
            'broker_id' => ['required'],
            'broker_login' => ['required'],
            'master_password' => ['required'],
            'account_proof' => ['required', 'image', 'max:2000'],
        ])->setAttributeNames([
            'broker_id' => trans('public.broker'),
            'broker_login' => trans('public.broker_login'),
            'master_password' => trans('public.master_password'),
            'account_proof' => trans('public.proof_of_account'),
        ])->validate();

        $user = Auth::user();

        // Check Account
        $account = BrokerAccount::where([
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
        ])->first();

        if (!empty($account) && $account->user_id != $user->id) {
            return back()->withErrors(['invalid_user' => trans('public.invalid_login_entered')]);
        }

        // Check Status
        if (!empty($account) && $account->status == 'pending') {
            return back()->withErrors(['status_pending' => trans('public.toast_account_pending_warning')]);
        }

        if (!empty($account) && $account->status == 'linked') {
            return back()->withErrors(['status_linked' => trans('public.toast_account_already_linked_warning')]);
        }

        $broker_account = BrokerAccount::where([
            'user_id' => $user->id,
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
        ])->first();

        if (empty($broker_account)) {
            $broker_account = BrokerAccount::create([
                'user_id' => $user->id,
                'broker_id' => $request->broker_id,
                'broker_login' => $request->broker_login,
            ]);
        }

        $broker_account->update([
            'master_password' => encrypt($request->master_password),
            'status' => 'pending',
        ]);

        if ($request->hasFile('account_proof')) {
            $broker_account->clearMediaCollection('account_proof');
            $broker_account->addMediaFromRequest('account_proof')->toMediaCollection('account_proof');
        }

        return back()->with('toast', 'success');
    }
}
