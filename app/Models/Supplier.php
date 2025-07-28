<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public $guarded = [];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function invoices()
    {
        return $this->hasMany(Supplier_invoice::class, 'supplier_id');
    }

    // protected static function booted()
    // {
    //     static::created(function ($supplier) {
    //         $supplier->account()->create([
    //             'name'     => 'حساب المورد: ' . $supplier->name,
    //             'type' => 'supplier',
    //             'total_capital_balance' => 0,
    //             'total_profit_balance' => 0,
    //         ]);
    //     });
    // }

}
