<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ExponseItem;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
class SalesController extends Controller
{
    public function add($id = null){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        if($id){
            $data['customer'] = Customer::findOrFail($id);
        }
        else {
            $data['customer_list'] = Customer::all();
        }
        $data['stock_category'] = Stock::with('category')->get();
        $data['exponse_list'] = ExponseItem::all();
        return view('customer.sales.add', $data);
    }
}
