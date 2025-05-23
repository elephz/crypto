<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function fiatAccounts()
    {
        return $this->hasMany(FiatAccount::class);
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function cryptoCurrenies()
    {
        return $this->belongsToMany(Currency::class, 'wallets', 'user_id', 'currency_id', 'id')
            ->withPivot('id','balance_total', 'balance_available', 'balance_locked', 'wallets_number');
    }

    public function fiatCurrenies()
    {
        return $this->belongsToMany(Currency::class, 'fiat_accounts', 'user_id', 'currency_id', 'id')
            ->withPivot('id','balance_total', 'balance_available', 'balance_locked', 'account_number');
    }
}
