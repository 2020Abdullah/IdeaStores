<?php

namespace App\Http\Controllers;

use App\Models\CustomerDue;
use Illuminate\Http\Request;

class DueController extends Controller
{
    public function index(){
        $dues = CustomerDue::latest()->paginate(100);
        return view('dues.index', compact('dues'));
    }
}
