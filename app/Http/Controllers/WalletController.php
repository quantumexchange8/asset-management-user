<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function getWalletHistory()
    {
        return Inertia::render('Wallet/WalletHistory');
    }
}
