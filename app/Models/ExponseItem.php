<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExponseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exponses(){
        return $this->hasMany(Exponse::class, 'expense_item_id');
    }

    public function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }
}
