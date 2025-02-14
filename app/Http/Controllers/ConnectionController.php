<?php

namespace App\Http\Controllers;

use App\Models\BrokerConnection;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConnectionController extends Controller
{
    public function index()
    {
        $connectionsCount = BrokerConnection::where('user_id', Auth::id())->count();

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
}
