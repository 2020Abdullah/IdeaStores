<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet_movement extends Model
{
    use HasFactory;

    public $guarded = [];

    public function movements(){
        return $this->belongsTo(Account_transactions::class, 'account_transaction_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
