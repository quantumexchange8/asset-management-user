<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExecuteBonusCommand extends Command
{
    protected $signature = 'execute:bonus';

    protected $description = 'Execute accumulated bonus';

    public function handle(): void
    {
        $users = User::where([
            'role' => 'user',
            'status' => 'active'
        ])
            ->get();

        foreach ($users as $user) {
            $bonus_wallet = $user->wallets()->where('type', 'bonus_wallet')->first();

            activity('bonus_execution')
                ->performedOn($bonus_wallet)
                ->withProperties([
                    'old' => $bonus_wallet->balance,
                    'balance' => $bonus_wallet->balance + $user->accumulated_amount,
                ])
                ->log('Executed ' . $user->accumulated_amount . ' to bonus wallet');

            $bonus_wallet->balance += $user->accumulated_amount;
            $bonus_wallet->save();

            $user->accumulated_amount = 0;
            $user->save();
        }
    }
}
