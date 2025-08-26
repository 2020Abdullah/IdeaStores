<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Stock_movement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(){
        $data['stocks'] = Stock::with('category')->paginate(100);
        return view('stocks.index', $data);
    }

    public function show($id){
        $data['stock'] = Stock::where('id', $id)->first();
        $data['stock_movments'] = Stock_movement::where('stock_id', $id)->paginate(100);
        return view('stocks.show', $data);
    }

    public function transctionFilter(Request $request){
        $query = Stock_movement::query();
        $stock = Stock::where('id', $request->stock_id)->first();

        if ($request->filled('moveType')) {
            $moveType = $request->moveType;
            $query->where('type', $moveType);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }

        $stock_movments = $query->orderBy('date', 'desc')->paginate(100);

        return view('stocks.transaction_table', ['stock_movments' => $stock_movments, 'stock' => $stock])->render();
    }

    public function getStockProducts(){
        $stocks = Stock::with('product')->select('id', 'product_id')->get();
        return response()->json([
            'status' => true,
            'data' => $stocks
        ]);
    }

    public function getStocks(Request $request){
        $stocks = Stock::with('category', 'size', 'unit', 'cost', 'movements')->findOrFail($request->stock_id);
        $initial_quantity = $stocks->movements()->where('type', 'in')->sum('quantity');
        $remaining_quantity = $stocks->movements()->sum('quantity');

        return response()->json([
            'status' => true,
            'data' => $stocks,
            'initial_quantity' => $initial_quantity,
            'remaining_quantity' => $remaining_quantity
        ]);
    }
}
