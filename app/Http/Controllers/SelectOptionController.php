<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DepositProfile;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;

class SelectOptionController extends Controller
{
    public function getCountries()
    {
        $countries = Country::select('id', 'name', 'phone_code', 'iso2', 'emoji', 'translations', 'currency', 'currency_symbol')
            ->get();

        return response()->json([
            'countries' => $countries,
        ]);
    }

    public function getDepositProfiles()
    {
        $query = DepositProfile::where('status', 'active');

        return response()->json([
            'depositProfiles' => (clone $query)->get(),
            'depositTypes' => (clone $query)->distinct('type')->pluck('type'),
        ]);
    }
}
