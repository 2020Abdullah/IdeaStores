<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class , 'customer_id');
    }

    public function invoice(){
        return $this->belongsTo(CustomerInvoices::class , 'customer_invoice_id');
    }
}
