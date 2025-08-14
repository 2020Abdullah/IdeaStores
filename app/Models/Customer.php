<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function invoices()
    {
        return $this->hasMany(CustomerInvoices::class, 'customer_id');
    }

    public function paymentTransactions()
    {
        return $this->morphMany(PaymentTransaction::class, 'related');
    }
}
