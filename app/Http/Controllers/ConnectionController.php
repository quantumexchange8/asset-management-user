<?php

namespace App\Http\Controllers;

use App\Models\BrokerConnection;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ConnectionController extends Controller
{
    public function index()
    {
        $connectionsCount = BrokerConnection::where('user_id', Auth::id())
            ->whereIn('status', [
                'active',
                'removed'
            ])
            ->count();

        return Inertia::render('Connections/BrokerConnection', [
            'connectionsCount' => $connectionsCount,
        ]);
    }

    public function getConnectionAccounts()
    {
        $query = BrokerConnection::with([
            'broker',
            'broker.media'
        ])
            ->where('user_id', Auth::id())
            ->whereIn('status', [
                'active',
                'removed'
            ])
            ->distinct('broker_id', 'broker_login')
            ->latest()
            ->get();

        return response()->json([
            'connections' => $query,
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

    public function connectBroker(Request $request)
    {
        Validator::make($request->all(), [
            'broker_id' => ['required'],
            'broker_login' => ['required'],
            'amount' => ['required', 'numeric', 'min:50'],
            'master_password' => ['required'],
        ])->setAttributeNames([
            'broker_id' => trans('public.broker'),
            'broker_login' => trans('public.broker_login'),
            'amount' => trans('public.amount'),
            'master_password' => trans('public.master_password'),
        ])->validate();

        // Check Wallet balance
        $cash_wallet = Wallet::where([
            'user_id' => Auth::id(),
            'type' => 'cash_wallet'
        ])->first();

        if ($cash_wallet->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

        // Check login
        $connection = BrokerConnection::where([
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
        ])->first();

        if (!empty($connection) && $connection->user_id != Auth::id()) {
            throw ValidationException::withMessages(['broker_login' => trans('public.invalid_login_entered')]);
        }

        $pending_connection = BrokerConnection::where([
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
            'status' => 'pending'
        ])->first();

        if ($pending_connection) {
            throw ValidationException::withMessages(['broker_login' => trans('public.wait_for_admin_to_verify')]);
        }

        $broker_connection = BrokerConnection::create([
            'user_id' => Auth::id(),
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
            'master_password' => encrypt($request->master_password),
            'capital_fund' => $request->amount,
            'connection_number' => RunningNumberService::getID('connection'),
            'status' => 'pending',
        ]);

        $existing_connection = BrokerConnection::where([
            'user_id' => Auth::id(),
            'broker_id' => $request->broker_id,
            'broker_login' => $request->broker_login,
        ])->first();

        if ($existing_connection->status == 'active' && $existing_connection->connection_type == 'initial_join') {
            $broker_connection->connection_type = 'top_up';
        } else {
            $broker_connection->connection_type = 'initial_join';
        }
        $broker_connection->save();

        $cash_wallet->balance -= $request->amount;
        $cash_wallet->save();

        return back()->with('toast', 'success');
    }
}
