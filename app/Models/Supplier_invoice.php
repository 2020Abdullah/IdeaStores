<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_invoice extends Model
{
    use HasFactory;

    public $guarded = [];

    public function items()
    {
        return $this->hasMany(Supplier_invoice_item::class, 'supplier_invoice_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function debts(){
        return $this->morphOne(ExternalDebts::class, 'debtable');
    }

    public function costs(){
        return $this->morphMany(Exponse::class, 'expenseable');
    }

    public function transaction(){
        return $this->morphOne(Account_transactions::class, 'related');
    }

}
