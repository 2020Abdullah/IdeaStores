<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account_transactions extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function related()
    {
        return $this->morphTo();
    }

    public function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }

}
