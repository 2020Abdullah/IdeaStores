<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInvoices extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function dues()
    {
        return $this->hasMany(CustomerDue::class, 'customer_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(CustomerInvoicesItem::class, 'customer_invoice_id');
    }

    public function costs(){
        return $this->morphMany(Exponse::class, 'expenseable');
    }
    
    public function debts(){
        return $this->morphOne(ExternalDebts::class, 'debtable');
    }

}
