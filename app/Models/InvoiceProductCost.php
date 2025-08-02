<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProductCost extends Model
{
    use HasFactory;

    public $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Supplier_invoice::class, 'invoice_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
