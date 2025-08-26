<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['balance'];

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

    public function dues()
    {
        return $this->hasMany(CustomerDue::class, 'customer_id');
    }

    public function movements(){
        return $this->morphMany(Stock_movement::class, 'related');
    }

    public function getBalanceAttribute()
    {
        // مجموع كل الفواتير (total_amount_invoice)
        $totalInvoices = (float) $this->invoices()->where('type', '!=' , 'cash')->sum('total_amount');

        // مجموع كل المدفوعات (نجعل القيم موجبة)
        $totalPayments = (float) $this->paymentTransactions()->sum('amount');

        // الرصيد = الفواتير - المدفوعات
        return $totalInvoices - $totalPayments;
    }
}
