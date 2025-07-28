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

    public function source()
    {
        return $this->morphTo();
    }

}
