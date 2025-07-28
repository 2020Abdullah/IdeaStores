<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice_supplier_cost extends Model
{
    use HasFactory;

    public $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Supplier_invoice::class, 'supplier_invoice_id');
    }
}
