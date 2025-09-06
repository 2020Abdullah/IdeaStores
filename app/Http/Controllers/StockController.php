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
    
        // إجمالي الكمية المتبقية لجميع الشحنات
        $remaining_quantity = $stock->movements()->sum('quantity');
    
        // الشحنات التصاعدية (FIFO) حسب أول دخول
        $shipments = $stock->movements()
            ->where('type', 'in')
            ->orderBy('id', 'asc') // أقدم شحنة أولاً
            ->get();
    
        $firstAvailableQty = 0;
        $firstCost = 0;
        $firstSuggestedPrice = 0;
    
        foreach ($shipments as $in) {
            $inQty = $in->quantity;
    
            // إجمالي الكمية الخارجة التي تخص نفس الشحنة
            $outQty = $stock->movements()
                ->where('type', 'out')
                ->where('source_code', $in->source_code)
                ->sum('quantity');
    
            $balance = $inQty - abs($outQty);
    
            if ($balance > 0) {
                // وجدنا أول شحنة بها رصيد
                $firstAvailableQty = $balance;
                $firstCost = InvoiceProductCost::where('source_code', $in->source_code)
                    ->value('cost_share') ?? 0;
                $firstSuggestedPrice = InvoiceProductCost::where('source_code', $in->source_code)
                    ->value('suggested_price') ?? 0;
                break; // نوقف عند أول شحنة متاحة
            }
        }
    
        return response()->json([
            'status' => true,
            'data' => $stock,
            'available_qty' => $firstAvailableQty,
            'cost' => $firstCost,
            'suggested_price' => $firstSuggestedPrice,
            'remaining_quantity' => $remaining_quantity
        ]);
    }
    
    
    
    
}
