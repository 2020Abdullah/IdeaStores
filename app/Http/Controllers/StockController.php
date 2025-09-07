<?php

namespace App\Http\Controllers;

use App\Models\InvoiceProductCost;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\Supplier_invoice;
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

        // إجمالي OUT (لو OUT بالسالب، ناخد القيمة المطلقة)
        $totalOut = abs($stock->movements()->where('type', 'out')->sum('quantity'));

        // شحنات IN بالترتيب الأقدم أولاً
        $ins = $stock->movements()
            ->where('type', 'in')
            ->orderBy('id', 'asc')
            ->get(['id','quantity','source_code']);

        $availableQty = 0;
        $cost = 0;
        $suggested = 0;
        $batchRef = null; // لمعرفة الشحنة الحالية (مثلاً SU-20252)

        foreach ($ins as $in) {
            $inQty = (int) $in->quantity;

            // لو إجمالي الخارج حتى الآن يغطي الشحنة بالكامل → انتقل للي بعدها
            if ($totalOut >= $inQty) {
                $totalOut -= $inQty;
                continue;
            }

            // هنا الشحنة دي لسه فيها جزء متاح
            $availableQty = $inQty - $totalOut; // المتبقي بعد خصم اللي اتصرف قبلها
            $batchRef = $in->source_code;

            // هات تكلفة نفس الشحنة
            // الخيار 1: لو عندك عمود source_code في جدول التكاليف
            $costRow = InvoiceProductCost::where('source_code', $batchRef)
                        ->where('stock_id', $stock->id)
                        ->first();

            $cost = $costRow->cost_share ?? 0;
            $suggested = $costRow->suggested_price ?? 0;

            break; // وقف عند أول شحنة متاحة
        }

        // لو مافيـش أي شحنة متاحة → كله صفر
        return response()->json([
            'status'           => true,
            'data'             => $stock,
            'batch_ref'        => $batchRef,        // كود الشحنة الحالية (للتوضيح في الواجهة)
            'available_qty'    => (int) $availableQty,
            'cost'             => (float) $cost,
            'suggested_price'  => (float) $suggested,
        ]);
    }

    
}
