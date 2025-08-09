<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function expenseable()
    {
        return $this->morphTo();
    }

    public function expenseItem()
    {
        return $this->belongsTo(ExponseItem::class, 'expense_item_id');
    }

    public function exponse(){
        return $this->morphTo();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
