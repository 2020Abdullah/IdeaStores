<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_invoice extends Model
{
    use HasFactory;

    public $guarded = [];

    public function costs()
    {
        return $this->hasMany(Invoice_supplier_cost::class, 'supplier_invoice_id');
    }

    public function items()
    {
        return $this->hasMany(Supplier_invoice_item::class, 'supplier_invoice_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
