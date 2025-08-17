<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $appends = ['balance'];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function invoices()
    {
        return $this->hasMany(Supplier_invoice::class, 'supplier_id');
    }

    public function paymentTransactions()
    {
        return $this->morphMany(PaymentTransaction::class, 'related');
    }

    public function getBalanceAttribute(): float
    {
        // مجموع كل الفواتير (total_amount_invoice)
        $totalInvoices = (float) $this->invoices()->where('invoice_type', '!=', 'cash')->sum('total_amount_invoice');

        // مجموع كل المدفوعات (نجعل القيم موجبة)
        $totalPayments = (float) $this->paymentTransactions()->sum('amount');

        // الرصيد = الفواتير - المدفوعات
        return $totalPayments - $totalInvoices;
    }

    protected static function booted()
    {
        static::created(function ($Supplier) {
            $Supplier->account()->create([
                'name'     => 'حساب مورد: ' . $Supplier->name,
                'type' => 'Supplier',
                'current_balance' => 0,
            ]);
        });
    }

}
