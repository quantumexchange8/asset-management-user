<?php

namespace App\Http\Controllers;

use App\Models\BonusHistory;
use App\Models\TradeRebateSummary;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $total_directs = $user->directs;
        $total_networks = $user->getChildrenIds();
        $total_bonus = BonusHistory::where('upline_user_id', $user->id)
            ->whereNot('bonus_type', 'personal_share')
            ->sum('bonus_amount');

        $total_rebate = TradeRebateSummary::where('upline_user_id', $user->id)
            ->whereNot('downline_user_id', $user->id)
            ->sum('rebate');

        return Inertia::render('Referrals/ReferralProgramme', [
            'total_directs' => count($total_directs),
            'total_networks' => count($total_networks),
            'total_earnings' => $total_rebate + $total_bonus,
        ]);
    }

    public function getReferralsData(Request $request)
    {
        $user = auth()->user();

        $query = $user->directs()
            ->with([
                'rank',
            ])
            ->select([
                'users.*',

                // Count total downlines
                DB::raw("(SELECT COUNT(*) FROM users AS u WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')) as total_downlines"),

                // Sum capital_fund from broker_connections where status is success
                DB::raw("COALESCE((SELECT SUM(capital_fund) FROM broker_connections WHERE broker_connections.user_id = users.id AND broker_connections.deleted_at is null AND broker_connections.connection_type != 'withdrawal' AND broker_connections.status = 'success'), 0) as capital_fund_sum"),

                // Sum total capital_fund of all downlines
                DB::raw("COALESCE((SELECT SUM(bc.capital_fund)
                  FROM broker_connections AS bc
                  JOIN users AS u ON bc.user_id = u.id
                  WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')
                    AND bc.deleted_at is null
                    AND bc.connection_type != 'withdrawal'
                    AND bc.status = 'success'), 0) as total_downline_capital_fund"),

                // Sum earnings from bonus_histories where this user is the downline (ignoring 'personal_share')
//                DB::raw("COALESCE((SELECT SUM(bh.bonus_amount)
//                  FROM bonus_histories AS bh
//                  WHERE bh.downline_user_id = users.id
//                    AND bh.bonus_type != 'personal_share'), 0) as direct_downline_bonus_earnings"),

                // Sum earnings from trade_rebate_summaries where this user is the downline (ignoring self-earned rows)
//                DB::raw("COALESCE((SELECT SUM(trs.rebate)
//                  FROM trade_rebate_summaries AS trs
//                  WHERE trs.downline_user_id = users.id
//                    AND trs.upline_user_id != trs.downline_user_id), 0) as direct_downline_rebate_earnings"),

                // Total earnings from direct downlines
                DB::raw("COALESCE(
                    (SELECT SUM(bh.bonus_amount)
                     FROM bonus_histories AS bh
                     WHERE bh.downline_user_id = users.id
                       AND bh.bonus_type != 'personal_share'), 0)
                 +
                 COALESCE(
                    (SELECT SUM(trs.rebate)
                     FROM trade_rebate_summaries AS trs
                     WHERE trs.downline_user_id = users.id
                       AND trs.upline_user_id = 'users.upline_id'
                       AND trs.upline_user_id != trs.downline_user_id), 0)
                 as total_direct_downline_earnings")
            ])
            ->orderByRaw('total_downline_capital_fund DESC')
            ->get();

        return response()->json([
            'referrals' => $query
        ]);
    }

    public function getDownlineData(Request $request)
    {
        $upline_id = $request->upline_id;
        $parent_id = $request->parent_id ?: Auth::id();

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $parent = User::query()
                ->where(function ($query) use ($search) {
                    $query->where('id_number', 'LIKE', $search)
                        ->orWhere('username', 'LIKE', $search)
                        ->orWhere('email', 'LIKE', $search);
                })
                ->first();

            if (empty($parent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found'
                ]);
            }

            $parent_id = $parent->id;
            $upline_id = $parent->upline_id;
        }

        $parent = User::with([
            'directs' => function ($query) {
                $query->select([
                    'users.*',

                    // Count total downlines
                    DB::raw("(SELECT COUNT(*) FROM users AS u WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')) as total_downlines"),

                    // Sum capital_fund from broker_connections where status is success
                    DB::raw("COALESCE((SELECT SUM(capital_fund) FROM broker_connections WHERE broker_connections.user_id = users.id AND broker_connections.deleted_at is null AND broker_connections.connection_type != 'withdrawal' AND broker_connections.status = 'success'), 0) as capital_fund_sum"),

                    // Sum total capital_fund of all downlines
                    DB::raw("COALESCE((SELECT SUM(bc.capital_fund)
                        FROM broker_connections AS bc
                        JOIN users AS u ON bc.user_id = u.id
                        WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')
                        AND u.id != users.id
                        AND bc.deleted_at is null
                        AND bc.connection_type != 'withdrawal'
                        AND bc.status = 'success'), 0) as total_downline_capital_fund")
                ]);
            }
        ])
            ->select([
                'users.*',

                // Count total downlines
                DB::raw("(SELECT COUNT(*) FROM users AS u WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')) as total_downlines"),

                // Sum capital_fund from broker_connections where status is active
                DB::raw("COALESCE((SELECT SUM(capital_fund) FROM broker_connections WHERE broker_connections.user_id = users.id AND broker_connections.deleted_at is null AND broker_connections.connection_type != 'withdrawal' AND broker_connections.status = 'success'), 0) as capital_fund_sum"),

                // Sum total capital_fund of all downlines
                DB::raw("COALESCE((SELECT SUM(bc.capital_fund)
                FROM broker_connections AS bc
                JOIN users AS u ON bc.user_id = u.id
                WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')
                AND u.id != users.id
                AND bc.deleted_at is null
                AND bc.connection_type != 'withdrawal'
                AND bc.status = 'success'), 0) as total_downline_capital_fund")
            ])
            ->where('id', $parent_id)
            ->first();

        $upline = User::select([
            'users.*',

            // Count total downlines
            DB::raw("(SELECT COUNT(*) FROM users AS u WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')) as total_downlines"),

            // Sum capital_fund from broker_connections where status is active
            DB::raw("COALESCE((SELECT SUM(capital_fund) FROM broker_connections WHERE broker_connections.user_id = users.id AND broker_connections.deleted_at is null AND broker_connections.connection_type != 'withdrawal' AND broker_connections.status = 'success'), 0) as capital_fund_sum"),

            // Sum total capital_fund of all downlines
            DB::raw("COALESCE((SELECT SUM(bc.capital_fund)
                FROM broker_connections AS bc
                JOIN users AS u ON bc.user_id = u.id
                WHERE u.hierarchyList LIKE CONCAT('%-', users.id, '-%')
                AND u.id != users.id
                AND bc.deleted_at is null
                AND bc.connection_type != 'withdrawal'
                AND bc.status = 'success'), 0) as total_downline_capital_fund")
        ])
            ->where('id', $upline_id)
            ->first();

        $parent_data = $this->formatUserData($parent);
        $upline_data = $upline ? $this->formatUserData($upline) : null;

        $direct_children = $parent->directs->map(function ($child) {
            return $this->formatUserData($child);
        });

        return response()->json([
            'success' => true,
            'upline' => $upline_data,
            'parent' => $parent_data,
            'direct_children' => $direct_children,
        ]);
    }

    private function formatUserData($user)
    {
        if (!$user) return null;

        if ($user->upline) {
            $upper_upline = $user->upline->upline;
        }

        return array_merge(
            $user->only(['id', 'username', 'id_number', 'upline_id', 'role']),
            [
                'upper_upline_id' => $upper_upline->id ?? null,
                'level' => $user->id === Auth::id() ? 0 : $this->calculateLevel($user->hierarchyList),
                'total_directs' => count($user->directs) ?? 0,
                'total_downlines' => $user->total_downlines ?? 0,
                'capital_fund_sum' => $user->capital_fund_sum ?? 0,
                'total_downline_capital_fund' => $user->total_downline_capital_fund ?? 0
            ]
        );
    }

    private function calculateLevel($hierarchyList)
    {
        if (is_null($hierarchyList) || $hierarchyList === '') {
            return 0;
        }

        $split = explode('-'.Auth::id().'-', $hierarchyList);
        return substr_count($split[1], '-') + 1;
    }

    public function getDownlineListingData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);
            $childrenIds = Auth::user()->getChildrenIds();

            $query = User::with([
                'upline',
                'active_connections',
                'rank'
            ])
                ->whereIn('id', $childrenIds)
                ->withSum('active_connections', 'capital_fund');

            if ($data['filters']['global']['value']) {
                $keyword = $data['filters']['global']['value'];

                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('username', 'like', '%' . $keyword . '%')
                        ->orWhereHas('upline', function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('username', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                });
            }

            if (!empty($data['filters']['start_join_date']['value']) && !empty($data['filters']['end_join_date']['value'])) {
                $start_join_date = Carbon::parse($data['filters']['start_join_date']['value'])->addDay()->startOfDay();
                $end_join_date = Carbon::parse($data['filters']['end_join_date']['value'])->addDay()->endOfDay();

                $query->whereBetween('created_at', [$start_join_date, $end_join_date]);
            }

            if ($data['sortField'] && $data['sortOrder']) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->orderByDesc('created_at');
            }

            $connections = $query->paginate($data['rows']);

            $connections->each(function ($user) {
                $user->level = $this->calculateLevel($user->hierarchyList);
            });

            return response()->json([
                'success' => true,
                'data' => $connections,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }
}
