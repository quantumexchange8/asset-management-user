<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function lot_commission(Request $request)
    {
        return Inertia::render('Report/LotCommission/LotCommission');
    }

    public function fetchHelloWorld()
    {
        // Return "Hello World" as a JSON response
        return response()->json(['message' => 'Hello World']);
    }
}
