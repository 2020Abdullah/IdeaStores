<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHouse\ProductAddRequest;
use App\Http\Requests\StoreHouse\StoreHouseRequest;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\StoreHouse;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;

class StoreHouseController extends Controller
{
    public function index(){
        $data['stocks'] = Stock::with('category')->paginate(100);
        return view('Stores.index', $data);
    }

    public function show($id){
        $data['stock'] = Stock::where('id', $id)->first();
        $data['stock_movments'] = Stock_movement::where('stock_id', $id)->paginate(100);
        return view('Stores.show', $data);
    }

    public function add(){
        return view('Stores.add');
    }

    public function store(StoreHouseRequest $request){
        $store = new StoreHouse();
        $store->name = $request->name;
        $store->phone = $request->phone;
        $store->address = $request->address;
        $store->save();
        return redirect()->route('storesHouse.index')->with('success', 'تم إضافة البيانات بنجاح');
    }

    public function addProduct(Request $request){
        try {
            $stock = new Stock();
            $stock->store_house_id = $request->store_house_id;
            $stock->category_id = $request->category_id;
            $stock->product_id = $request->product_id;
            $stock->batch_number = $request->batch_number;
            $stock->unit_id = $request->unit_id;
            $stock->initial_quantity = $request->initial_quantity;
            $stock->remaining_quantity = $request->remaining_quantity;
            $stock->save();
            return back()->with('success', 'تم إضافة المنتج إلي المخزون بنجاح');
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}
