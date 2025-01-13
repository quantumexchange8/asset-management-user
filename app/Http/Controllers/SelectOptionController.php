<?php

namespace App\Http\Controllers;

use App\Models\Country;
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

}
