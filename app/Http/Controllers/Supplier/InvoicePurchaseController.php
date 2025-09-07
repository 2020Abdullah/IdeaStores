<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Invoices\supplierInvoiceRequest;
use App\Http\Requests\PaymentInvoiceRequest;
use App\Models\Account_transactions;
use App\Models\App;
use App\Models\Category;
use App\Models\Exponse;
use App\Models\ExponseItem;
use App\Models\ExternalDebts;
use App\Models\InvoiceProductCost;
use App\Models\paymentTransaction;
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\StoreHouse;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use App\Models\Unit;
use App\Models\Wallet;
use App\Models\Warehouse;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class InvoicePurchaseController extends Controller
{
    protected $user_id;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                $this->user_id = auth()->user()->id; 
            } else {
                $this->user_id = null;
                auth()->logout();
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $data['invoices_list'] = Supplier_invoice::where('user_id', $this->user_id)
            ->orderBy('invoice_date', 'desc')
            ->paginate(100);

        $data['warehouse_list'] = Warehouse::all();
        return view('suppliers.invoices.index', $data);
    }
    
 
    public function add($id = null)
    {
        $data['warehouse_list'] = Warehouse::all();
        if ($id) {
            $data['supplier'] = Supplier::where('user_id', $this->user_id)->findOrFail($id);
        } else {
            $data['suppliers_list'] = Supplier::where('user_id', $this->user_id)->get();
        }
        $data['main_categories'] = Category::whereNull('parent_id')->get();
        $data['exponse_list'] = ExponseItem::where('is_profit', 0)->get();

        return view('suppliers.invoices.add', $data);
    }

    public function store(supplierInvoiceRequest $request)
    {
        try {
            if ($request->invoice_type === 'opening_balance') {
                $this->addOpenBalance($request);
            } elseif ($request->invoice_type === 'cash') {
                $this->cash($request);
            } else {
                $this->credit($request);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الفاتورة بنجاح',
                'redirect' => route('supplier.invoice.index') // لو عايز تحويل بعد النجاح
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
    

    protected function updateStock($request, $invoice)
    {
        $invoice_items = $request->input('items');
        $main_store = StoreHouse::latest()->first();
    
        if (!$invoice_items || !is_array($invoice_items)) {
            return;
        }
    
        // ==================== حذف الأصناف القديمة ====================
        $invoice->items()->delete();
    
        // ==================== التعامل مع كل صنف ====================
        foreach ($invoice_items as $item) {
    
            // حفظ الأصناف في فاتورة المورد
            $invoice->items()->create([
                'supplier_invoice_id' => $invoice->id,
                'category_id'         => $item['category_id'],
                'product_id'          => $item['product_id'],
                'unit_id'             => $item['unit_id'],
                'size'                => $item['size'] ?? null,
                'quantity'            => $item['quantity'],
                'pricePerMeter'       => $item['pricePerMeter'],
                'length'              => $item['length'],
                'purchase_price'      => $item['purchase_price'],
                'total_price'         => $this->normalizeNumber($item['total_price']),
            ]);
    
            // البحث عن المخزون أو إنشاؤه إذا لم يكن موجود
            $stock = Stock::firstOrCreate(
                [
                    'category_id' => $item['category_id'],
                    'product_id'  => $item['product_id'],
                ],
                [
                    'size'            => $item['size'] ?? null,
                    'unit_id'         => $item['unit_id'],
                    'store_house_id'  => $main_store->id,
                    'date'            => $request->invoice_date,
                    'user_id'         => $this->user_id,
                ]
            );
    
            // حساب الكمية والتكلفة الأساسية
            $unit = Unit::findOrFail($item['unit_id']);
            $quantity_to_add = (strtolower($unit->symbol) === 'سم') 
                ? $item['length'] * $item['quantity'] 
                : $item['quantity'];
    
            $unit_cost = $this->normalizeNumber($item['total_price']) / $quantity_to_add;
    
            // توزيع التكاليف الإضافية
            if ($request->additional_cost > 0) {
                $total_invoice_amount = $this->normalizeNumber($request->total_amount_invoice);
                $general_cost         = $this->normalizeNumber($request->additional_cost);
                $item_total_price     = $this->normalizeNumber($item['total_price']);
    
                $item_percentage      = $item_total_price / $total_invoice_amount;
                $cost_share           = ($item_percentage * $general_cost) + $item_total_price;
    
                $unit_cost_with_share = $cost_share / $quantity_to_add;
            } else {
                $unit_cost_with_share = $unit_cost;
            }
    
            // تسجيل أو تحديث حركة المخزون
            Stock_movement::updateOrCreate(
                [
                    'source_code' => $invoice->invoice_code,
                    'stock_id'    => $stock->id,
                ],
                [
                    'related_type' => Supplier::class,
                    'related_id'   => $request->supplier_id,
                    'type'         => 'in',
                    'quantity'     => $quantity_to_add,
                    'note'         => 'شراء',
                    'date'         => $invoice->invoice_date,
                    'user_id'      => $this->user_id,
                ]
            );
    
            // ==================== حفظ أو تحديث تكلفة الصنف ====================
            InvoiceProductCost::updateOrCreate(
                [
                    'supplier_invoice_id' => $invoice->id,
                    'stock_id'            => $stock->id,
                ],
                [
                    'base_cost'   => $this->normalizeNumber($unit_cost),
                    'cost_share'  => $this->normalizeNumber($unit_cost_with_share),
                    'source_code' => $invoice->invoice_code,
                    'date'        => $invoice->invoice_date,
                ]
            );
        }
    }
    
    
    
    protected function credit($request){
        DB::beginTransaction();
        try {
            $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
            $supplier = Supplier::findOrFail($request->supplier_id);
    
            // إنشاء الفاتورة الجديدة بدون دفع مع إضافة user_id
            $invoice = Supplier_invoice::create([
                'supplier_id' => $supplier->id,
                'user_id' => $this->user_id, // ربط الفاتورة بالمستخدم الحالي
                'invoice_code' => $this->generateNum(),
                'invoice_date' => $request->invoice_date,
                'invoice_type' => $request->invoice_type,
                'total_amount' => $total_amount_invoice,
                'total_amount_invoice' => $total_amount_invoice,
                'cost_price' => $request->additional_cost,
                'paid_amount' => 0,
                'invoice_staute' => 0, // غير مدفوعة افتراضياً
                'notes' => $request->notes,
            ]);
    
            // إضافة التكاليف وحركات المخزن
            $this->updateCost($request, $invoice);
    
            // تحديث حالة الفواتير ورصيد المورد أولاً بأول
            $this->updateInvoiceState($request);
    
            // تحديث المخزون
            $this->updateStock($request, $invoice);
    
            DB::commit();
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    
    
    
    protected function generateNum()
    {
        $year = date('Y');

        // الحصول على آخر كود فاتورة يبدأ بـ SU-2025
        $lastInvoice = Supplier_invoice::where('invoice_code', 'like', 'SU-' . $year . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastInvoice) {
            // استخراج الرقم بعد SU-2025، مثلاً: SU-20253 -> 3
            $lastNumber = (int) str_replace('SU-' . $year, '', $lastInvoice->invoice_code);
            $newNumber = $lastNumber + 1;
        } else {
            // أول فاتورة
            $newNumber = 1;
        }

        $invoice_code = 'SU-' . $year . $newNumber;
        return $invoice_code;
    }

    protected function normalizeNumber($number, $max = 9999999999999.99)
    {
        // إزالة الفواصل وتحويل لعدد
        $value = (float) str_replace(',', '', trim($number));
    
        // لو القيمة سالبة، نعيدها صفر
        if ($value < 0) {
            $value = 0;
        }
    
        // تحديد الحد الأقصى حتى لا يحدث Overflow
        if ($value > $max) {
            $value = $max;
        }
    
        // إرجاع الرقم مع خانتين عشريتين فقط
        return round($value, 2);
    }
    

    protected function cash($request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
        $total_amount = $this->normalizeNumber($request->total_amount);
        $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
        $current_balance = $this->normalizeNumber($request->current_balance);
    
        // إنشاء الفاتورة مع إضافة user_id
        $invoice = Supplier_invoice::create([
            'supplier_id' => $request->supplier_id,
            'user_id' => $this->user_id,  // ربط الفاتورة بالمستخدم الحالي
            'invoice_code' => $this->generateNum(),
            'invoice_date' => $request->invoice_date,
            'invoice_type' => $request->invoice_type,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $total_amount_invoice,
            'paid_amount' => $total_amount_invoice,
            'invoice_staute' => 1,
            'cost_price' => $request->additional_cost,
            'notes' => $request->notes,
            'warehouse_id' => $request->warehouse_id,
            'wallet_id' => $request->wallet_id,
        ]);
    
        // إضافة التكاليف في البنود الصحيحة
        $this->updateCost($request, $invoice);
    
        // تسجيل حركة الحساب
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'wallet_id' => $request->wallet_id,
            'direction'  => 'out',
            'amount'     => -$total_amount_invoice,
            'transaction_type' => 'purchase',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->notes ?? 'دفع فاتورة مورد كاش',
            'source_code' => $invoice->invoice_code,
            'date' => $invoice->invoice_date,
            'user_id'        => $this->user_id,
        ]);
    
        // ضبط المخزون
        $this->updateStock($request, $invoice);

    }
    

    protected function updateCost($request, $invoice)
    {
        $costs = $request->input('costs');
        $default_warehouse = Warehouse::where('is_default', 1)->first();
        $default_wallet = Wallet::where('is_default', 1)->first();

        if ($costs && is_array($costs)) {
            // امسح التكاليف القديمة كلها
            $invoice->costs()->delete();

            // أضف التكاليف الجديدة
            foreach ($costs as $cost) {
                $invoice->costs()->create([
                    'expense_item_id' => $cost['exponse_id'],
                    'account_id'      => $default_warehouse->account->id,
                    'amount'          => -$this->normalizeNumber($cost['amount']),
                    'note'            => $cost['note'] ?? 'تكاليف إضافية',
                    'date'            => $cost['date'] ?? $invoice->invoice_date,
                    'source_code'     => $invoice->invoice_code,
                    'user_id'     => $this->user_id,
                ]);
            }

            // تحديث أو إنشاء معاملة مالية
            if ($invoice->transaction) {
                $invoice->transaction()->update([
                    'amount' => -$this->normalizeNumber($request->additional_cost),
                ]);
            } else {
                Account_transactions::create([
                    'account_id'       => $default_warehouse->account->id,
                    'direction'        => 'out',
                    'wallet_id'        => $default_wallet->id,
                    'amount'           => -$this->normalizeNumber($request->additional_cost),
                    'transaction_type' => 'expense',
                    'related_type'     => Supplier_invoice::class,
                    'related_id'       => $invoice->id,
                    'description'      => $request->notes ?? 'مصروفات فواتير موردين',
                    'source_code'      => $invoice->invoice_code,
                    'date'             => $invoice->invoice_date,
                    'user_id'             => $this->user_id,
                ]);
            }

        } else {
            // لو مفيش تكاليف → احذف الترانزكشن لو موجود
            $transaction = Account_transactions::where('source_code', $invoice->invoice_code)->first();
            if ($transaction) {
                $transaction->delete();
            }
        }
    }


    protected function addOpenBalance($request)
    {
        DB::beginTransaction();
        try {
            $total_amount_invoice = $this->normalizeNumber($request->opening_balance_value);
    
            // تأكد أنه لا يوجد رصيد افتتاحي سابق للمورد لنفس المستخدم
            $exists = Supplier_invoice::where([
                'supplier_id' => $request->supplier_id,
                'invoice_type' => 'opening_balance',
                'user_id' => $this->user_id
            ])->exists();
    
            if ($exists) {
                return back()->with('error', 'هذا المورد لديه رصيد افتتاحي من قبل');
            }
    
            // إنشاء الفاتورة (رصيد افتتاحي) مع إضافة user_id
            $invoice = Supplier_invoice::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => $this->user_id,
                'invoice_code' => $this->generateNum(),
                'invoice_date' => $request->invoice_date,
                'invoice_type' => $request->invoice_type,
                'invoice_staute' => 0, // غير مدفوعة مبدئياً
                'total_amount' => $total_amount_invoice,
                'total_amount_invoice' => $total_amount_invoice,
                'paid_amount' => 0,
                'notes' => $request->notes,
            ]);
              
            // تحديث الفواتير أولاً بأول وإعادة حساب رصيد المورد    
            $this->updateInvoiceState($request);
    
            DB::commit();
            return redirect()->route('supplier.index')
                ->with('success', 'تم عمل رصيد افتتاحي للمورد');
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
    

    protected function updateOpenBalance($request)
    {
        DB::beginTransaction();
        try {
            $invoiceToUpdate = Supplier_invoice::findOrFail($request->id);
            $supplier = Supplier::findOrFail($invoiceToUpdate->supplier_id);

            $newAmount = $this->normalizeNumber($request->opening_balance);

            // تحديث بيانات الفاتورة
            $invoiceToUpdate->update([
                'invoice_date' => $request->invoice_date,
                'total_amount' => $newAmount,
                'total_amount_invoice' => $newAmount,
                'notes' => $request->notes,
            ]);

            $this->updateInvoiceState($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    protected function updateInvoiceState($request)
    {
        $supplier = Supplier::findOrFail($request->supplier_id);
    
        // مجموع المدفوعات المتاحة
        $available = $this->normalizeNumber(
            $supplier->paymentTransactions()->sum(DB::raw('ABS(amount)'))
        );
    
        // معالجة الفواتير على دفعات لتجنب استهلاك الذاكرة
        Supplier_invoice::where('supplier_id', $supplier->id)
            ->where('invoice_type', '!=', 'cash')
            ->orderBy('invoice_date', 'asc')
            ->chunkById(500, function ($invoices) use (&$available, $supplier) {
    
                foreach ($invoices as $invoice) {
    
                    $invoiceAmount = $this->normalizeNumber($invoice->total_amount_invoice);
    
                    // جلب الدين الحالي إذا وجد
                    $debt = $invoice->debts()->first();
    
                    // لا يوجد مبلغ متاح بعد الآن
                    if ($available <= 0) {
                        if (!$debt) {
                            $invoice->debts()->create([
                                'description' => 'دين كامل على الفاتورة للمورد ' . $supplier->name,
                                'amount'      => -$invoiceAmount,
                                'paid'        => 0,
                                'remaining'   => $invoiceAmount,
                                'is_paid'     => 0,
                                'date'        => $invoice->invoice_date,
                                'user_id'     => $this->user_id,
                            ]);
                        } else {
                            $debt->update([
                                'amount'    => -$invoiceAmount,
                                'paid'      => 0,
                                'remaining' => $invoiceAmount,
                                'is_paid'   => 0,
                            ]);
                        }
                        $invoice->update(['paid_amount' => 0, 'invoice_staute' => 0]);
                        continue;
                    }
    
                    // دفع كامل
                    if ($available >= $invoiceAmount) {
                        $invoice->update(['paid_amount' => $invoiceAmount, 'invoice_staute' => 1]);
                        if ($debt) {
                            $debt->delete();
                        }
                        $available -= $invoiceAmount;
                        continue;
                    }
    
                    // دفع جزئي
                    if ($available > 0 && $available < $invoiceAmount) {
                        $paidNow = $available;
                        $invoice->update(['paid_amount' => $paidNow, 'invoice_staute' => 2]);
    
                        if ($debt) {
                            $debt->update([
                                'amount'    => -$invoiceAmount,
                                'paid'      => $paidNow,
                                'remaining' => $invoiceAmount - $paidNow,
                                'is_paid'   => 0,
                            ]);
                        } else {
                            $invoice->debts()->create([
                                'description' => 'دين جزئي على الفاتورة للمورد ' . $supplier->name,
                                'amount'      => -$invoiceAmount,
                                'paid'        => $paidNow,
                                'remaining'   => $invoiceAmount - $paidNow,
                                'is_paid'     => 0,
                                'date'        => $invoice->invoice_date,
                                'user_id'     => $this->user_id,
                            ]);
                        }
    
                        $available = 0;
                        continue;
                    }
                }
            });
    }
    
    
    protected function updateCredit($request)
    {
        DB::beginTransaction();
        try {
            $invoice = Supplier_invoice::findOrFail($request->id);
            $cost_total = $this->normalizeNumber($request->additional_cost);
            $newAmount = $this->normalizeNumber($request->total_amount_invoice);
            $total_amount = $this->normalizeNumber($request->total_amount);

            $invoice->invoice_date = $request->invoice_date;
            $invoice->total_amount = $total_amount;
            $invoice->total_amount_invoice = $newAmount;
            $invoice->cost_price = $cost_total;
            $invoice->notes = $request->notes;
            $invoice->save();

            // تحديث التكاليف إن وجدت
            $this->updateCost($request, $invoice);

            // تحديث حالة كل فاتورة

            $this->updateInvoiceState($request);

            // ضبط المخزون بعد التعديل
            $this->updateStock($request, $invoice);

            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم تحديث الفاتورة بنجاح.');

        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    protected function updateCash($request)
    {
        DB::beginTransaction();
        try {
            $invoice = Supplier_invoice::findOrFail($request->id);
    
            $total_amount = $this->normalizeNumber($request->total_amount);
            $newAmount    = $this->normalizeNumber($request->total_amount_invoice);
            $cost_total   = $this->normalizeNumber($request->additional_cost);
    
            // تحديث بيانات الفاتورة
            $invoice->update([
                'invoice_date'         => $request->invoice_date,
                'total_amount'         => $total_amount,
                'total_amount_invoice' => $newAmount,
                'paid_amount'          => $newAmount, // كاش → مدفوع بالكامل
                'cost_price'           => $cost_total,
                'notes'                => $request->notes,
            ]);
    
            // تحديث التكاليف الإضافية
            $costs = $request->input('costs');
            if ($costs && is_array($costs)) {
                foreach ($costs as $cost) {
                    $exponse = Exponse::where('expense_item_id', $cost['exponse_id'])->first();
                    if ($exponse) {
                        $exponse->update([
                            'amount' => $cost['amount']
                        ]);
                        $invoice->transaction()->update([
                            'amount' => -$cost_total
                        ]);
                    }
                }
            }
    
            // تعديل حركة المعاملات (الخزنة) حسب المبلغ الجديد
            Account_transactions::where('source_code', $invoice->invoice_code)->update([
                'amount' => -$total_amount
            ]);

            // ضبط المخزون بعد التعديل
            $this->updateStock($request, $invoice);
    
            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم تحديث فاتورة المورد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
    
    public function edit($id){
        $data['warehouse_list'] = Warehouse::all();
        $data['invoice'] = Supplier_invoice::findOrFail($id);
        $data['suppliers_list'] = Supplier::all();
        $data['finalCategories'] = Category::doesntHave('children')->get();
        $data['products'] = Product::with('category')->get();
        $data['units'] = Unit::all();
        $data['sizes'] = Size::all();
        $data['exponse_list'] = ExponseItem::where('is_profit', 0)->get();
        return view('suppliers.invoices.edit', $data);
    }

    public function update(supplierInvoiceRequest $request)
    {
        try {
            if($request->invoice_type === 'opening_balance'){
                $this->updateOpenBalance($request);
            }
            elseif($request->invoice_type === 'credit'){
                $this->updateCredit($request);
            }
            else {
                $this->updateCash($request);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الفاتورة بنجاح',
                'redirect' => route('supplier.invoice.index')
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }    

    public function show($code){
        if(auth()->user()->type !== 1){
            return back()->with('warning', 'غير مسموح لك بالدخول لهذه الصفحة');
        }
        $invoice = Supplier_invoice::with('supplier')->where('invoice_code', $code)->first();
        $app = App::latest()->first();
        return view('suppliers.invoices.show', compact('invoice', 'app'));
    }

    public function download($id){
        $invoice = Supplier_invoice::findOrFail($id);
        $app = App::latest()->first();

        $html = view('suppliers.invoices.invoice-pdf', compact('invoice', 'app'))->render();

        // إعداد mPDF بدعم RTL واللغة العربية
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans',
            'default_font_size' => 12
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('invoice_'.$invoice->invoice_code.'.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function payment(PaymentInvoiceRequest $request){
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
    
        $amount = $this->normalizeNumber($request->amount);
        
        // تسجيل حركة معاملة في الخزنة
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'out',
            'wallet_id'     => $request->wallet_id,
            'amount'     => -$amount,
            'transaction_type' => 'payment',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->description,
            'user_id'        => $this->user_id,
        ]);
        
        // تسجيل دفعة للمورد
        paymentTransaction::create([
            'related_type' => Supplier::class,
            'related_id' => $supplier->id,
            'direction' => 'in',
            'amount' => $amount,
            'payment_date' => now()->toDateString(),
            'wallet_id' => $request->wallet_id,
            'description' => $request->description ?? 'دفعة مقدمة',
            'user_id'        => $this->user_id,
        ]);

        $this->updateInvoiceState($request);
    
        return back()->with('success', 'تم دفع الدفعة بنجاح');
    }  

    public function filter(Request $request){
        $query = Supplier_invoice::query()->where('user_id', $this->user_id);

        if ($request->filled('searchText')) {
            $searchText = $request->searchText;
    
            $query->where(function ($q) use ($searchText) {
                $q->where('invoice_code', 'like', '%' . $searchText . '%')
                  ->orWhereHas('supplier', function($q2) use ($searchText) {
                      $q2->where('name', 'like', '%' . $searchText . '%');
                  });
            });
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }

        $invoices_list = $query->orderBy('invoice_date', 'desc')->paginate(100);

        return view('suppliers.invoices.invoice_table', ['invoices_list' => $invoices_list])->render();
    }

    public function filterBySupplier(Request $request)
    {
        $query = Supplier_invoice::query()
            ->where('supplier_id', $request->supplier_id); 
        if ($request->filled('searchCode')) {
            $query->where('invoice_code', $request->searchCode);
        }
    
        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }
    
        if ($request->filled('invoice_staute')) {
            if ($request->invoice_staute === 'unpaid') {
                $query->where(function ($q) {
                    $q->where('invoice_staute', 0)
                      ->orWhere('invoice_staute', 2);
                });
            } else {
                $query->where('invoice_staute', $request->invoice_staute);
            }
        }
    
        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
    
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }
    
        $invoices_list = $query->orderBy('invoice_date', 'desc')->paginate(100);
    
        return view('suppliers.invoices.invoice_table', [
            'invoices_list' => $invoices_list
        ])->render();
    }
    

    public function deleteInv(Request $request)
    {
        DB::beginTransaction();
        try {
            $invoice = Supplier_invoice::findOrFail($request->id);
            $supplier = Supplier::findOrFail($request->supplier_id);
            $warehouse = Warehouse::where('is_default' , 1)->first();
    
            // حذف الدين أو المعاملة المالية حسب حالة الفاتورة
            if ($invoice->debts) {
                $invoice->debts()->delete();
            } else {
                $invoice->transaction()->create([
                    'account_id' => $warehouse->account->id,
                    'direction' => 'in',
                    'wallet_id' => $invoice->wallet_id,
                    'transaction_type' => 'refund',
                    'amount' => $invoice->total_amount_invoice,
                    'related_type' => Supplier::class,
                    'related_id' => $supplier->id,
                    'description' => 'مرتجع',
                    'source_code' => $invoice->invoice_code,
                    'date' => now(),
                    'user_id'        => $this->user_id,
                ]);
                $supplier->paymentTransactions()->delete();
            }

            // حذف حركة الستوك المتعلقة بالفاتورة
            Stock_movement::where('source_code', $invoice->invoice_code)->delete();

            // حذف الفاتورة نفسها
            $invoice->delete();

            DB::commit();
    
            return redirect()->route('supplier.account.show', $supplier->id)
                             ->with('success', 'تم حذف الفاتورة وتحديث الإستوك بنجاح');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function returnedInvoices(){
        $invoices_list = Supplier_invoice::onlyTrashed()->where('user_id', $this->user_id)->orderBy('deleted_at', 'desc')->paginate(100);
        return view('suppliers.invoices.returned', compact('invoices_list'));
    }

    public function filterReturn(Request $request){
        $query = Supplier_invoice::onlyTrashed()->where('user_id', $this->user_id);

        if ($request->filled('searchText')) {
            $searchText = $request->searchText;
    
            $query->where(function ($q) use ($searchText) {
                $q->where('invoice_code', 'like', '%' . $searchText . '%')
                  ->orWhereHas('supplier', function($q2) use ($searchText) {
                      $q2->where('name', 'like', '%' . $searchText . '%');
                  });
            });
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }

        $invoices_list = $query->orderBy('deleted_at', 'desc')->paginate(100);

        return view('suppliers.invoices.invoice_return_table', ['invoices_list' => $invoices_list])->render();
    }

}
