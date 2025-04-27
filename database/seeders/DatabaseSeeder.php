<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\FiatAccount;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        $this->call([
            CurrencySeeder::class,
            FiatAccountSeeder::class,
            WalletSeeder::class,
            CurrencySeeder::class,
            ExchangeRateSeeder::class,
            WalletTranferSeeder::class,
            OrderSeeder::class,
            SalesOrderSeeder::class,
            // Add other seeders here
        ]);
    }
}
