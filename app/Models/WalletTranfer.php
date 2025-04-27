<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTranfer extends Model
{
    use HasFactory;

    public function from()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    public function to()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }
}
