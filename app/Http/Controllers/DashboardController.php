<?php

namespace App\Http\Controllers;

use App\Models\BrokerConnection;
use App\Models\WalletLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeConnections = BrokerConnection::where('status', 'active');

        $current_asset_capital = (clone $activeConnections)
            ->where('user_id', $user->id)
            ->sum('capital_fund');

        $current_team_capital = (clone $activeConnections)
            ->whereIn('user_id', $user->getChildrenIds())
            ->sum('capital_fund');

        $query = WalletLog::query()
            ->where([
                'user_id' => $user->id,
                'category' => 'bonus'
            ]);

        $total_bonus = (clone $query)
            ->sum('amount');

        $latest_bonus = (clone $query)->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('amount', '>', 0)
            ->latest()
            ->get();

        return Inertia::render('Dashboard/Dashboard', [
            'currentAssetCapital' => $current_asset_capital,
            'currentTeamCapital' => $current_team_capital,
            'totalBonus' => $total_bonus,
            'latestBonuses' => $latest_bonus,
        ]);
    }
}
