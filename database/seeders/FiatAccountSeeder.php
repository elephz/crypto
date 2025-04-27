<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\FiatAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class FiatAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = Currency::select("id")->where('type', 'FIAT')->get();
        foreach (User::doesntHave("fiatAccounts")->get() as $user) {
            foreach ($currencies as $currency) {
                $balanceTotal = rand(1000, 10000);
                $balanceAvailable = rand(0, $balanceTotal);
                $balanceLocked = $balanceTotal - $balanceAvailable;
                FiatAccount::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'currency_id' => $currency->id,
                    ],
                    [
                        'balance_total' => $balanceTotal,
                        'balance_available' => $balanceAvailable,
                        'balance_locked' => $balanceLocked,
                        'account_number' => '12345678' . $user->id . $currency->id,
                    ]
                );
            }
        }
    }
}
