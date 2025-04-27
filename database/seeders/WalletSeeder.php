<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = Currency::select("id")->where('type', 'CRYPTO')->get();
        foreach (User::doesntHave("wallet")->get() as $user) {
            foreach ($currencies as $currency) {
                $balanceTotal = rand(1000, 10000);
                $balanceAvailable = rand(0, $balanceTotal);
                $balanceLocked = $balanceTotal - $balanceAvailable;
                Wallet::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'currency_id' => $currency->id,
                    ],
                    [
                        'balance_total' => $balanceTotal,
                        'balance_available' => $balanceAvailable,
                        'balance_locked' => $balanceLocked,
                        'wallets_number' => '12345678' . $user->id . $currency->id,
                    ]
                );
            }
        }
    }
}
