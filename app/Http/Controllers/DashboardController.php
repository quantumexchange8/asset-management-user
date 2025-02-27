<?php

namespace App\Http\Controllers;

use App\Models\BrokerConnection;
use App\Models\User;
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

        $lastSaturday = Carbon::now()->subDays(Carbon::now()->dayOfWeek + 1)->startOfDay();

        $total_bonus = WalletLog::query()
            ->where([
                'user_id' => $user->id,
                'category' => 'bonus'
            ])
            ->where('created_at', '<=', $lastSaturday)
            ->sum('amount');

        return Inertia::render('Dashboard/Dashboard', [
            'currentAssetCapital' => $current_asset_capital,
            'currentTeamCapital' => $current_team_capital,
            'totalBonus' => $total_bonus,
        ]);
    }

    public function admin_login(Request $request, $hashedToken)
    {
        $users = User::all();

        foreach ($users as $user) {
            $dataToHash = md5($user->name . $user->email . $user->id_number);

            if ($dataToHash === $hashedToken) {

                $admin_id = $request->admin_id;
                $admin_name = $request->admin_name;

//                Activity::create([
//                    'log_name' => 'access_portal',
//                    'description' => $admin_name . ' with ID: ' . $admin_id . ' has access user ' . $user->name . ' with ID: ' . $user->id ,
//                    'subject_type' => User::class,
//                    'subject_id' => $user->id,
//                    'causer_type' => User::class,
//                    'causer_id' => $admin_id,
//                    'event' => 'access_portal',
//                ]);

                Auth::login($user);
                return redirect()->route('dashboard');
            }
        }

        return redirect()->route('login')->with('toast', [
            'title' => trans('public.access_denied'),
            'type' => 'error'
        ]);
    }
}
