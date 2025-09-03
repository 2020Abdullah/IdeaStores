<?php

namespace App\Http\Controllers;

use App\Models\InvoiceProductCost;
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

    public function getStocks(Request $request)
    {
        $stock = Stock::with('category', 'size', 'unit')
            ->findOrFail($request->stock_id);
    
        // إجمالي الكمية المتبقية
        $remaining_quantity = $stock->movements()->sum('quantity');
    
        // هات كل الشحنات in لهذا المخزون بالترتيب التنازلي
        $shipments = $stock->movements()
            ->where('type', 'in')
            ->orderByDesc('id')
            ->get();
    
        $lastCost = 0;
    
        foreach ($shipments as $in) {
            // إجمالي الكمية الواردة
            $inQty = $in->quantity;
    
            // إجمالي الكمية الخارجة اللي تخص نفس الفاتورة
            $outQty = $stock->movements()
                ->where('type', 'out')
                ->where('source_code', $in->source_code)
                ->sum('quantity');
    
            // الرصيد المتبقي من الشحنة
            $balance = $inQty - abs($outQty);
    
            if ($balance > 0) {
                // جلب التكلفة من جدول التكاليف
                $lastCost = InvoiceProductCost::where('source_code', $in->source_code)
                    ->value('cost_share') ?? 0;
                break; // أول شحنة نلاقيها فيها رصيد = هي اللي نوقف عندها
            }
        }
    
        return response()->json([
            'status' => true,
            'data' => $stock,
            'cost' => $lastCost,
            'remaining_quantity' => $remaining_quantity
        ]);
    }
    
    
    
}
