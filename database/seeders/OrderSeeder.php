<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereHas("wallet", fn ($wallet) => $wallet->where('balance_available', '>', 0))
            ->limit(10)
            ->inRandomOrder()
            ->get();

        foreach ($users as $user) {
            $currency = $user->cryptoCurrenies
                ->filter(fn ($currency) => $currency->pivot->balance_available > 0)
                ->random();

            Order::create([
                'user_id' => $user->id,
                'currency_id' => $currency->id,
                'price' => rand(100, 1000), // fiat price
                'amount_total' => $currency->pivot->balance_available,
                'amount_available' => $currency->pivot->balance_available,
                'amount_locked' => 0,
            ]);
        }
    }
}
