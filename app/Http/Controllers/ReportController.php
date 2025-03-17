<?php

namespace App\Http\Controllers;

use App\Models\BonusHistory;
use App\Models\TradeRebateSummary;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function profit_sharing()
    {
        return Inertia::render('Report/Profit/ProfitSharing');
    }

    public function getProfitSharingData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);

            $query = BonusHistory::with([
                'subject_user:id,username,hierarchyList',
                'broker',
                'broker.media',
            ])
                ->where([
                    'upline_user_id' => Auth::id(),
                    'bonus_type' => 'personal_share'
                ]);

            if ($data['filters']['global']['value']) {
                $keyword = $data['filters']['global']['value'];

                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhereHas('subject_user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhere('bonus_type', 'like', '%' . $keyword . '%');
                });
            }

            if (!empty($data['filters']['start_join_date']['value']) && !empty($data['filters']['end_join_date']['value'])) {
                $start_join_date = Carbon::parse($data['filters']['start_join_date']['value'])->addDay()->startOfDay();
                $end_join_date = Carbon::parse($data['filters']['end_join_date']['value'])->addDay()->endOfDay();

                $query->whereBetween('created_at', [$start_join_date, $end_join_date]);
            }

            $maxBonusAmount = (clone $query)
                ->orderByDesc('bonus_amount')
                ->first()
                ?->bonus_amount;

            if ($data['sortField'] && $data['sortOrder']) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->orderByDesc('created_at');
            }

            $totalBonusAmount = (clone $query)
                ->sum('bonus_amount');

            $connections = $query->paginate($data['rows']);

            return response()->json([
                'success' => true,
                'data' => $connections,
                'totalBonusAmount' => $totalBonusAmount,
                'maxBonusAmount' => $maxBonusAmount ?? 0,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    public function ib_group_incentive()
    {
        return Inertia::render('Report/Standard/StandardBonus');
    }

    public function getStandardBonusData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);

            $query = BonusHistory::with([
                'subject_user:id,username,hierarchyList',
                'broker',
                'broker.media',
            ])
                ->where('upline_user_id', Auth::id())
                ->whereNot('bonus_type', 'personal_share');

            if ($data['filters']['global']['value']) {
                $keyword = $data['filters']['global']['value'];

                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhereHas('subject_user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhere('bonus_type', 'like', '%' . $keyword . '%');
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

            $totalBonusAmount = (clone $query)
                ->sum('bonus_amount');

            $maxBonusAmount = (clone $query)
                ->orderByDesc('bonus_amount')
                ->first()
                ?->bonus_amount;

            $connections = $query->paginate($data['rows']);

            return response()->json([
                'success' => true,
                'data' => $connections,
                'totalBonusAmount' => $totalBonusAmount,
                'maxBonusAmount' => $maxBonusAmount ?? 0,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    public function rebate_bonus()
    {
        return Inertia::render('Report/Rebate/RebateBonus');
    }

    public function getRebateBonusData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);

            $query = TradeRebateSummary::with([
                'subject_user:id,username,hierarchyList',
                'broker',
                'broker.media',
            ])
                ->where([
                    'upline_user_id' => Auth::id(),
                    'status' => 'approved',
                ]);

            if ($data['filters']['global']['value']) {
                $keyword = $data['filters']['global']['value'];

                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhereHas('subject_user', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%')
                                ->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    })->orWhere('symbol', 'like', '%' . $keyword . '%');
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

            $totalVolume = (clone $query)
                ->sum('volume');

            $totalRebate = (clone $query)
                ->sum('rebate');

            $connections = $query->paginate($data['rows']);

            return response()->json([
                'success' => true,
                'data' => $connections,
                'totalVolume' => $totalVolume,
                'totalRebate' => $totalRebate ?? 0,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }
}
