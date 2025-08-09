<?php

namespace App\Http\Controllers;

use App\Models\ExternalDebts;
use Illuminate\Http\Request;

class ExternalDebtsController extends Controller
{
    public function index(){
        $debts = ExternalDebts::latest()->paginate(100);
        return view('debts.index', compact('debts'));
    }
}
