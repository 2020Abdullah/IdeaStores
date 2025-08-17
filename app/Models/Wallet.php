<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = ['balance'];

    protected $dates = ['date'];

    public function warehouse()
    {
        return $this->belongsToMany(Warehouse::class, 'wallet_warehouse');
    }

    public function transactions()
    {
        return $this->hasMany(Account_transactions::class, 'wallet_id');
    }

    public function getBalanceAttribute(): float
    {
        $in = $this->transactions()->where('direction', 'in')->sum('amount');
        $out = $this->transactions()->where('direction', 'out')->sum('amount');
        return $in + $out;
    }

}
