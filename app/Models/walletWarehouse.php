<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class walletWarehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $table = "wallet_warehouse";
}
