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

    public function debts(){
        return $this->morphOne(ExternalDebts::class, 'debtable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getBalanceAttribute()
    {
        // مجموع الفواتير بعد الخصم (غير الكاش) مباشرة في قاعدة البيانات
        $totalInvoices = $this->invoices()
            ->where('type', '!=', 'cash')
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN discount_type = 'percent' THEN total_amount_without_discount - (total_amount_without_discount * discount_value / 100)
                        WHEN discount_type = 'value' THEN total_amount_without_discount - discount_value
                        ELSE total_amount_without_discount
                    END
                ) as total_after_discount
            ")
            ->value('total_after_discount');
    
        $totalInvoices = (float) ($totalInvoices ?? 0);
    
        // مجموع المدفوعات
        $totalPayments = (float) $this->paymentTransactions()->sum('amount');
    
        // الرصيد = الفواتير بعد الخصم - المدفوعات
        return $totalInvoices - $totalPayments;
    }
    


    protected static function booted()
    {
        static::created(function ($customer) {
            $customer->account()->create([
                'name'     => 'حساب عميل: ' . $customer->name,
                'type' => 'Supplier',
            ]);
        });
    }
}
