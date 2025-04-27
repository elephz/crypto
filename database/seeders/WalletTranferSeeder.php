<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTranfer;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletTranferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // Transfer amount from one wallet to another
    public function run()
    {
        $items = rand(1, 10);
        for ($i = 0; $i < $items; $i++) {
            $this->createTransfer();
        }
    }

    public function createTransfer()
    {
        $amount = rand(1, 100);
        $currency = Currency::select("id")->where("type", "CRYPTO")->inRandomOrder()->first();
        $from = Wallet::where('balance_available', '>', $amount)
            ->where("currency_id", $currency->id)
            ->inRandomOrder()
            ->first();

        $to = Wallet::where('user_id', '!=', $from->user_id)
            ->where("currency_id", $currency->id)
            ->inRandomOrder()
            ->first();

        try {
            DB::beginTransaction();

            if (!$from || !$to) {
                throw new \Exception("Wallet not found");
            }

            if ($from->user_id == $to->user_id) {
                throw new \Exception("Cannot transfer to the same user");
            }

            if ($from->currency_id != $to->currency_id) {
                throw new \Exception("Currency mismatch");
            }

            if ($from->balance_available < $amount) {
                throw new \Exception("Insufficient balance in {$from->wallets_number}");
            }

            $walletTranfer = WalletTranfer::create([
                'from_wallet_id' => $from->id,
                'to_wallet_id' => $to->id,
                'amount' => $amount,
                'fee' => 0,
                'status' => 'completed',
            ]);

            $from->balance_available -= $amount;
            $from->save();
            WalletTransaction::create([
                'user_id' => $from->user_id,
                'type' => 'transfer_out',
                'tranfer_id' => $walletTranfer->id,
                'wallet_id' => $from->id,
                'amount' => $amount,
                'fee' => 0,
                'balance' => $from->balance_available,
                'transaction_number' => 'TRF-' . uniqid(),
                'description' => "Transfer to {$to->user->name} ({$to->wallets_number})",
            ]);

            $to->balance_available += $amount;
            $to->save();
            WalletTransaction::create([
                'user_id' => $to->user_id,
                'type' => 'transfer_in',
                'tranfer_id' => $walletTranfer->id,
                'wallet_id' => $to->id,
                'amount' => $amount,
                'fee' => 0,
                'balance' => $to->balance_available + $amount,
                'transaction_number' => 'TRF-' . uniqid(),
                'description' => "Transfer from {$from->user->name} ({$from->wallets_number})",
            ]);

            DB::commit();
            echo "Transfer successful: {$amount} from {$from->wallets_number} to {$to->wallets_number} \n";
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle exception if needed
            echo "Transfer failed: " . $e->getMessage() . "\n";
        }
    }
}
