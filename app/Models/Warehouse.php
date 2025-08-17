<?php

namespace App\Models;

use App\Traits\HasBalance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, HasBalance;

    protected $guarded = [];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function wallets()
    {
        return $this->belongsToMany(Wallet::class, 'wallet_warehouse');
    }

}
