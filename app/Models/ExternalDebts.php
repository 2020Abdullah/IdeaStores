<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDebts extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function debtable()
    {
        return $this->morphTo();
    }
}
