<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrokerController extends Controller
{
    public function index()
    {
        $brokerCounts = Broker::where('status', 'active')->count();

        return Inertia::render('Broker/Broker', [
            'brokerCounts' => $brokerCounts
        ]);
    }

    public function getBrokerData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true);

            $query = Broker::query()
                ->with([
                    'user:id,name',
                ])
                ->withCount(['connections as connections_count' => function($query) {
                    $query->select(DB::raw('count(distinct(user_id))'));
                }])
                ->withSum('connections', 'capital_fund');

            //global filter
            if ($data['filters']['global']['value']) {
                $query->where(function ($q) use ($data) { //function() allow to add more condition' use ($data) means $data is passed into the clause to be use
                    $keyword = $data['filters']['global']['value'];

                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            }

            //status filter
            if ($data['filters']['status']['value']) {
                $query->where('status', $data['filters']['status']['value']);
            }

            //sort order
            if ($data['sortOrder']) {
                $sortType = $data['sortOrder'];
                switch ($sortType) {
                    case 'latest':
                        $query->orderBy('created_at', 'desc');
                        break;

                    case 'largest_fund':
                        $query->orderByDesc('connections_sum_capital_fund');
                        break;

                    case 'most_investors':
                        $query->orderByDesc('connections_count');
                        break;

                    default:
                        return response()->json(['error' => 'Invalid filter'], 400);
                }
            } else {
                $query->latest();
            }

            $brokers = $query->paginate($data['rows']);

            foreach ($brokers as $broker) {
                $broker->broker_image = $broker->getMedia('broker_image')->map(function ($media) {
                    return $media->getUrl();  // Return the media URL
                });
            }

            return response()->json([
                'success' => true,
                'data' => $brokers,
            ]);
        }
        return response()->json(['success' => false, 'data' => []]);
    }
}
