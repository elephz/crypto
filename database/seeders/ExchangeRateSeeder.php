<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = Currency::select("id", "type")->get();
        $fiat = $currencies->where('type', 'FIAT')->pluck("id");
        $crypto = $currencies->where('type', 'CRYPTO')->pluck("id");

        foreach ($fiat as $fiatCurrency) {
            foreach ($crypto as $cryptoCurrency) {
                ExchangeRate::firstOrCreate(
                    [
                        'from_currency_id' => $fiatCurrency,
                        'to_currency_id' => $cryptoCurrency,
                    ],
                    [
                        'rate' => rand(0.1, 10),
                    ]
                );
            }
        }
    }
}
