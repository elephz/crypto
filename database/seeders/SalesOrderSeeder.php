<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\SalesOrder;
use App\Models\FiatAccount;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\FiatAccountTransaction;

class SalesOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::all();

        foreach ($orders as $order) {

            //ต้องการซื้อ n เหรียญ
            $amount = rand(10, 100);

            // ตรวจสอบว่าผู้ขายมีเงินเพียงพอหรือไม่
            if (($order->amount_available - $order->amount_locked) < $amount) {
                throw new \Exception("Insufficient balance in {$order->user->name} ({$order->amount_available})");
            }

            // ผู้ซื้อ
            $user = User::where('id', '!=', $order->user_id)
                ->whereHas('fiatAccounts', function ($query) use ($amount) {
                    $query->where('balance_available', '>=', $amount);
                })
                ->with("fiatCurrenies")
                ->first();
            
            if (!$user) {
                dump("No buyer found with sufficient balance");
                continue;
            }

            // ตรวจสอบว่าผู้ซื้อมีบัญชีเงินสดหรือไม่
            if ($user->fiatAccounts->isEmpty()) {
                dump("User {$user->name} has no fiat account");
                continue;
            }

            // สกุลเงินที่ผู้ซื้อใช้
            $currency = $user->fiatCurrenies->random();

            //get last rate
            $exchangeRate = ExchangeRate::select("id", "rate")->where('from_currency_id', $currency->id)
                ->where('to_currency_id', $order->currency_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$exchangeRate) {
                // rate not support
                dump("Exchange rate not found for {$currency->name} to {$order->currency->name}");
                continue;
            }

            // จำนวนเงินที่ต้องจ่าย
            $price = $amount * $exchangeRate->rate;

            // เช็คว่าผู้ซื้อมีเงินเพียงพอหรือไม่
            if ($currency->pivot->balance_available < $price) {
                dump("Insufficient balance in {$user->name} ({$currency->pivot->balance_available})");
                continue;
            }
            
            $arr = ['pending', 'completed', 'cancelled'];
            $status = array_rand($arr, 1);
            $status = $arr[$status];

            try {
                DB::beginTransaction();
               
                // สร้างคำสั่งซื้อใหม่
                $salesOrder = SalesOrder::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'order_number' => 'SO-' . strtoupper(uniqid()),
                    'fiat_account_id' => $currency->pivot->id,
                    'amount' => $amount,
                    'price' => $price,
                    'rate' => $exchangeRate->rate,
                    'exchange_rate_id' => $exchangeRate->id,
                    'status' => $status,
                ]);

                // pending || completed จะมีการ lock เงิน
                if($status != 'cancelled') {
                    FiatAccount::where('user_id', $user->id)
                        ->where('currency_id', $currency->id)
                        ->update([
                            'balance_available' => DB::raw('balance_available - ' . $price) ,
                            'balance_locked' => DB::raw('balance_locked + ' . $price),
                        ]);
    
                    $order->update([
                        'amount_available' => $order->amount_available - $amount,
                        'amount_locked' => $order->amount_locked + $amount,
                    ]);
    
                    Wallet::where('user_id', $order->user_id)
                        ->where('currency_id', $order->currency_id)
                        ->update([
                            'balance_available' => DB::raw('balance_available - ' . $amount),
                            'balance_locked' => DB::raw('balance_locked + ' . $amount),
                        ]);
                }

                if ($status == 'completed') {
                    // การทำธุรกรรมเสร็จสิ้น
                    // อัปเดตยอดเงินในบัญชีของผู้ซื้อ

                    $fiatAccount = FiatAccount::where('user_id', $user->id)
                        ->where('currency_id', $currency->id)
                        ->first();

                    $fiatAccount->balance_total = $fiatAccount->balance_total - $price;
                    $fiatAccount->balance_locked = $fiatAccount->balance_locked - $price;
                    $fiatAccount->balance_available = $fiatAccount->balance_total - $fiatAccount->balance_locked;
                    $fiatAccount->save();

                    FiatAccountTransaction::create([
                        'user_id' => $user->id,
                        'fiat_account_id' => $fiatAccount->id,
                        'type' => 'withdrawal',
                        'sales_order_id' => $salesOrder->id,
                        'amount' => $price,
                        'fee' => 0,
                        'balance' => $fiatAccount->balance_available,
                        'transaction_number' => 'TRF-' . uniqid(),
                        'description' => "Withdrawal from sales order {$salesOrder->order_number}",
                    ]);

                    // อัปเดตยอดที่เหลือในคำสั่งซื้อ
                    $order->update([
                        'amount_locked' =>  $order->amount_locked - $amount,
                    ]);

                    // อัปเดตยอดเงินในกระเป๋าของผู้ขาย
                    $wallet = Wallet::where('user_id', $order->user_id)
                        ->where('currency_id', $order->currency_id)
                        ->first();

                    $wallet->balance_locked = $wallet->balance_locked - $amount;
                    $wallet->balance_total = $wallet->balance_total + $amount;
                    $wallet->balance_available = ($wallet->balance_total + $amount) - $wallet->balance_locked - $amount;
                    $wallet->save();

                    // อัปเดต Transaction
                    WalletTransaction::create([
                        'user_id' => $order->user_id,
                        'type' => 'topup',
                        'sales_order_id' => $salesOrder->id,
                        'wallet_id' => $wallet->id,
                        'amount' => $amount,
                        'fee' => 0,
                        'balance' => $wallet->balance_available,
                        'transaction_number' => 'TRF-' . uniqid(),
                        'description' => "Topup from sales order {$salesOrder->order_number}",
                    ]);
                }

                DB::commit();
                dump("Sales Order Created: {$salesOrder->order_number}");
     
            } catch (\Exception $e) {
                DB::rollBack();
                echo "Error: {$e->getMessage()} \n";
                echo "Line: {$e->getLine()} \n";
                continue; // ข้ามไปหาผู้ซื้อคนถัดไป
            }
        }

    }
}
