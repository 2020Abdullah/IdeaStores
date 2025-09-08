<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSupplier(){
        $suppliers = Supplier::all();
        return response()->json(
            [
            'statue' => true,
            'data'   => $suppliers
            ]
        );
    }
}
