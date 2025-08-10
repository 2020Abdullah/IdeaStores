<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentTransaction extends Model
{
    use HasFactory;

    public $guarded = [];

    
    /**
     * الجهة المرتبطة (عميل أو مورد)
     */
    public function related()
    {
        return $this->morphTo();
    }

    /**
     * مصدر الدفع (فاتورة بيع أو شراء)
     */
    public function source()
    {
        return $this->morphTo();
    }
}
