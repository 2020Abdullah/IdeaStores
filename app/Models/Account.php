<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['balance', 'profit_balance', 'total_balance'];

    public function accountable()
    {
        return $this->morphTo();
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Account_transactions::class, 'account_id');
    }

    public function relateable()
    {
        return $this->hasMany(Account_transactions::class, 'related_id');
    }

    // رصيد الربحية فقط
    public function getProfitBalanceAttribute(): float
    {
        return $this->transactions()->sum('profit_amount');
    }

    // الرصيد الكلي (الرصيد الحالي + ربحية)
    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->profit_balance;
    }


    public function getBalanceAttribute(): float
    {
        return $this->transactions()->sum('amount');
    }
}
