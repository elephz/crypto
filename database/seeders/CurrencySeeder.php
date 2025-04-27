<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency = [
            [
                'code' => 'USD',
                'name' => 'United States Dollar',
                'type' => 'FIAT',
                'symbol' => '$',
            ],
            [
                'code' => 'THB',
                'name' => 'Thai Baht',
                'type' => 'FIAT',
                'symbol' => '฿',
            ],
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'type' => 'CRYPTO',
                'symbol' => '₿',
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',
                'type' => 'CRYPTO',
                'symbol' => 'Ξ',
            ],
            [
                'code' => 'DOGE',
                'name' => 'DOGECOIN',
                'type' => 'CRYPTO',
                'symbol' => 'BNB',
            ],
            [
                'code' => 'XRP',
                'name' => 'Ripple',
                'type' => 'CRYPTO',
                'symbol' => 'XRP',
            ],
        ];

        foreach ($currency as $curr) {
            Currency::firstOrCreate(
                ['code' => $curr['code']],
                $curr
            );
        }
    }
}
