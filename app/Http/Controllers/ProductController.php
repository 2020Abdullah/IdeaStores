<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductRequest;
use App\Models\Category;
use App\Models\InvoiceProductCost;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        return view('product.index', compact('products'));
    }

    public function add(){
        $data['categories'] = Category::all();
        $data['units'] = Unit::all();
        return view('product.add', $data);
    }

    public function edit($id){
        $data['categories'] = Category::doesntHave('children')->get();
        $data['product'] = Product::findOrFail($id);
        return view('product.edit', $data);
    }

    public function store(ProductRequest $request){
        try {
            $product = new Product();
            $product->category_id = $request->final_category_id;
            $product->name = $request->name;
            $product->save();
            return redirect()->route('product.index')->with('success', 'تم حفظ البيانات بنجاح');
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function update(ProductRequest $request){
        try {
            $product = Product::findOrFail($request->id);
            $product->category_id = $request->final_category_id;
            $product->name = $request->name;
            $product->save();
            return redirect()->route('product.index')->with('success', 'تم تحديث البيانات بنجاح');
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function delete(Request $request){
        try {
            $product = Product::findOrFail($request->id);
            $product->delete();
            return back()->with('success', 'تم حذف البيانات بنجاح');
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function getProducts(Request $request)
    {
        $products = Product::where('category_id', $request->category_id)->get();
    
        return response()->json([
            'data' => $products
        ]);
    }

    public function showPrice(){
        $ProductCost = InvoiceProductCost::paginate(100);
        return view('product.price', compact('ProductCost'));
    }

    public function updatePrice(Request $request){
        $rate = $request->rate / 100;
        $price_sale = InvoiceProductCost::where('id', $request->id)->first();
    
        if ($price_sale) {
            $price_sale->rate = $request->rate; // تخزن النسبة كما هي (مثلاً 20)
            $price_sale->suggested_price = $request->cost_price + ($request->cost_price * $rate);
            $price_sale->save();
        }
        
        return back()->with('success', 'تم حذف البيانات بنجاح');
    }
}
