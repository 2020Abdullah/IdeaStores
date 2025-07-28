<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $data['suppliersCount'] = Supplier::count();
        $data['invoicesCount'] = Supplier_invoice::count();
        return view('dashboard', $data);
    }
}
