<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoices\customerInvoiceRequest;
use App\Models\Account_transactions;
use App\Models\App;
use App\Models\Customer;
use App\Models\CustomerInvoices;
use App\Models\ExponseItem;
use App\Models\ExternalDebts;
use App\Models\paymentTransaction;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\Wallet;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class SalesController extends Controller
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

    public function index(){
        $data['invoices_list'] = CustomerInvoices::orderBy('date', 'DESC')
        ->paginate(100);
        return view('customer.sales.index', $data);
    }
    public function add($id = null){
        $data['warehouse_list'] = Warehouse::all();
        if($id){
            $data['customer'] = Customer::findOrFail($id);
        }
        else {
            $data['customer_list'] = Customer::all();
        }
        $data['stock_category'] = Stock::with('category')->get();
        $data['exponse_list'] = ExponseItem::where('is_profit', 0)->get();
        $data['wallets'] = Wallet::all();
        return view('customer.sales.add', $data);
    }

    public function edit($id){
        $data['invoice'] = CustomerInvoices::findOrFail($id);
        $data['warehouse_list'] = Warehouse::all();
        $data['wallets'] = Wallet::all();
        $data['exponse_list'] = ExponseItem::where('is_profit', 0)->get();
        // جلب الكمية المتاحة لكل منتج من جدول الحركات
        foreach ($data['invoice']->items as $item) {
            $item->stock = Stock::where('category_id', $item->category_id)->where('product_id', $item->product_id)->first();
        }
        return view('customer.sales.edit', $data);
    }

    public function store(customerInvoiceRequest $request){
        try {
            if($request->invoice_type === 'opening_balance'){
                return $this->addOpenBalance($request);
            }
            elseif($request->invoice_type === 'credit'){
                return $this->credit($request);
            }
            else {
                return $this->cash($request);
            }
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الفاتورة بنجاح',
                'redirect' => route('customer.invoice.index') // لو عايز تحويل بعد النجاح
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(customerInvoiceRequest $request){
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
                'message' => 'تم تحديث الفاتورة بنجاح',
                'redirect' => route('customer.invoice.index')
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateOpenBalance($request){
        DB::beginTransaction();
        try {
            // 1. تحديث الفاتورة 
            $invoice = CustomerInvoices::findOrFail($request->id);
            $invoice->date = $request->date;
            $invoice->total_amount = $this->normalizeNumber($request->total_amount_invoice);
            $invoice->total_amount_without_discount = $this->normalizeNumber($request->total_amount_invoice);
            $invoice->notes = $request->notes;
            $invoice->save();
    
            // 2. تحديث حالة الفواتير والمبالغ المستحقة
            $this->updateInvoiceState($request);
    
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    public function updateCredit($request){
        DB::beginTransaction();
        try {
            // 1. تحديث الفاتورة 
            $invoice = CustomerInvoices::findOrFail($request->id);
            $invoice->date = $request->date;
            $invoice->total_amount_without_discount = $this->normalizeNumber($request->total_amount_without_discount);
            $invoice->total_amount = $this->normalizeNumber($request->total_amount);
            $invoice->notes = $request->notes;
            $invoice->cost_price = $request->additional_cost;
            $invoice->discount_type = $request->discount_type;
            $invoice->discount_value = $request->discount_value;
            $invoice->save();

            // 2. تحديث الإستوك
            $this->updateStock($request, $invoice);
    
            // 3. تحديث حالة الفواتير والمبالغ المستحقة
            $this->updateInvoiceState($request);

            // 4. تحديث التكاليف إن وجدت
            $this->updateCost($request, $invoice);

            // 5. توزيع الربحية
            $this->ProfitDistribution($invoice);
    
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    public function updateCash($request){
        DB::beginTransaction();
        try {
            $total_amount = $this->normalizeNumber($request->total_amount);
            $total_amount_without_discount = $this->normalizeNumber($request->total_amount_without_discount);
            $total_profit_inv = $this->normalizeNumber($request->total_profit_inv);
    
            // حساب المبلغ بعد الخصم
            $finalAmount = $total_amount_without_discount;
            if ($request->discount_type && $request->discount_value) {
                if ($request->discount_type === 'percent') {
                    $finalAmount -= ($total_amount_without_discount * $request->discount_value / 100);
                } else {
                    $finalAmount -= $request->discount_value;
                }
            }
    
            // 1. تعديل الفاتورة
            $invoice = CustomerInvoices::findOrFail($request->id);
            $invoice->update([
                'date' => $request->date,
                'total_amount_without_discount' => $total_amount_without_discount,
                'total_amount' => $total_amount,
                'paid_amount' => $finalAmount,
                'total_profit' => $total_profit_inv,
                'cost_price' => $request->additional_cost,
                'notes' => $request->notes ?? '',
                'discount_type' => $request->discount_type ?? '',
                'discount_value' => $request->discount_value,
            ]);
    
            // 2. ضبط المخزن وخصم البضاعة
            $this->updateStock($request, $invoice);
    
            // 3. تعديل أو إنشاء حركة الخزنة
            $transaction = Account_transactions::where('source_code', $invoice->code)->first();
    
            if ($transaction) {
                $transaction->amount = $finalAmount;
                $transaction->profit_amount = $total_profit_inv;
                $transaction->description = $request->notes ?? 'تحصيل فاتورة مبيعات كاش';
                $transaction->save();
            } else {
                // إذا لم يكن موجودًا يمكن إنشاء عملية جديدة
                // لاحظ: تأكد من تمرير warehouse_id و wallet_id المناسبين
                Account_transactions::create([
                    'account_id'       => $request->warehouse_account_id ?? null,
                    'wallet_id'        => $request->wallet_id ?? null,
                    'direction'        => 'in',
                    'amount'           => $finalAmount,
                    'profit_amount'    => $total_profit_inv,
                    'transaction_type' => 'sale',
                    'related_type'     => Customer::class,
                    'related_id'       => $request->customer_id,
                    'description'      => $request->notes ?? 'تحصيل فاتورة مبيعات كاش',
                    'source_code'      => $invoice->code,
                    'date'             => $invoice->date,
                    'user_id'          => $this->user_id,
                ]);
            }
    
            // 4. تحديث التكاليف إن وجدت
            $this->updateCost($request, $invoice);
    
            // 5. توزيع الربحية
            $this->ProfitDistribution($invoice);
    
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
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
    

    protected function generateNum()
    {
        $year = date('Y');

        // الحصول على آخر كود فاتورة يبدأ بـ SU-2025
        $lastInvoice = CustomerInvoices::where('code', 'like', 'CU-' . $year . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastInvoice) {
            // استخراج الرقم بعد SU-2025، مثلاً: SU-20253 -> 3
            $lastNumber = (int) str_replace('CU-' . $year, '', $lastInvoice->code);
            $newNumber = $lastNumber + 1;
        } else {
            // أول فاتورة
            $newNumber = 1;
        }

        $invoice_code = 'CU-' . $year . $newNumber;
        return $invoice_code;
    }

    protected function updateCost($request, $invoice)
    {
        $costs = $request->input('costs');
        $default_warehouse = Warehouse::where('is_default', 1)->first();
        $default_wallet = Wallet::where('is_default', 1)->first();

        if ($costs && is_array($costs) && count($costs)) {

            // 1) امسح كل التكاليف الحالية
            $invoice->costs()
            ->whereHas('expenseItem', fn($q) => $q->where('is_profit', 0))
            ->delete();

            // 2) أنشئ التكاليف الجديدة
            foreach ($costs as $cost) {
                $invoice->costs()->create([
                    'expense_item_id' => $cost['exponse_id'],
                    'account_id'      => $default_warehouse->account->id,
                    'type'          => $cost['type'],
                    'amount'          => -$this->normalizeNumber($cost['amount']),
                    'note'            => $cost['note'] ?? 'تكاليف إضافية علي فاتورة بيع',
                    'date'            => $invoice->date,
                    'source_code'     => $invoice->code,
                    'user_id'     => $this->user_id,
                ]);
            }

            // 3) حدّث أو أنشئ الترانزكشن
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
                    'related_type'     => CustomerInvoices::class,
                    'related_id'       => $invoice->id,
                    'description'      => $request->notes ?? 'مصروفات فواتير مبيعات',
                    'source_code'      => $invoice->code,
                    'date'             => $invoice->date,
                    'user_id'     => $this->user_id,
                ]);
            }

        } else {
            // لو مفيش تكاليف
            // امسح الترانزكشن
            if ($invoice->transaction) {
                $invoice->transaction()->delete();
            }

            // امسح فقط البنود غير الربحية المرتبطة بالفاتورة
            $invoice->costs()
                ->whereHas('expenseItem', fn($q) => $q->where('is_profit', 0))
                ->delete();
        }
    }

    protected function addOpenBalance($request)
    {
        DB::beginTransaction();
        try {
            $total_amount_invoice = $this->normalizeNumber($request->opening_balance_value);

            // تأكد أنه لا يوجد رصيد افتتاحي سابق
            $exists = CustomerInvoices::where([
                'customer_id' => $request->customer_id,
                'type' => 'opening_balance'
            ])->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا العميل لديه رصيد افتتاحي من قبل'
                ], 500);
            }

            // إنشاء الفاتورة (رصيد افتتاحي)
            $invoice = CustomerInvoices::create([
                'customer_id' => $request->customer_id,
                'code' => $this->generateNum(),
                'date' => $request->date,
                'type' => $request->invoice_type,
                'staute' => 0, 
                'total_amount' => $total_amount_invoice,
                'total_amount_without_discount' => $total_amount_invoice,
                'paid_amount' => 0,
                'notes' => $request->notes,
                'user_id' => $this->user_id,
            ]);
              
            // تحديث الفواتير أولاً بأول وإعادة حساب رصيد المورد    
            $this->updateInvoiceState($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    protected function credit($request){
        DB::beginTransaction();
        try {
            $total_amount_without_discount = $this->normalizeNumber($request->total_amount_without_discount);
            $total_amount_invoice = $this->normalizeNumber($request->total_amount);
            $total_profit_inv = $this->normalizeNumber($request->total_profit_inv);
            // 1. تسجيل الفاتورة 
            $invoice = CustomerInvoices::create([
                'customer_id' => $request->customer_id,
                'code' => $this->generateNum(),
                'date' => $request->date,
                'type' => $request->invoice_type,
                'total_amount_without_discount' => $total_amount_without_discount,
                'total_amount' => $total_amount_invoice,
                'total_profit' => $total_profit_inv,
                'cost_price' => $request->additional_cost,
                'paid_amount' => 0,
                'staute' => 0, 
                'notes' => $request->notes,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'user_id' => $this->user_id,
            ]);

            // 2. إعادة حساب الأرصدة وضبطها 
            $this->updateInvoiceState($request);

            // 3. خصم الكمية المباعة من المخزن
            $this->updateStock($request, $invoice);

            // 4. تحديث التكاليف إن وجدت
            $this->updateCost($request, $invoice);

            // 5. توزيع الربحية
            $this->ProfitDistribution($invoice);

            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    protected function cash($request)
    {
        DB::beginTransaction();
        try {
            $total_amount = $this->normalizeNumber($request->total_amount);
            $total_amount_without_discount = $this->normalizeNumber($request->total_amount_without_discount);
            $total_profit_inv = $this->normalizeNumber($request->total_profit_inv);
    
            // حساب المبلغ بعد الخصم
            $finalAmount = $total_amount_without_discount;
            if ($request->discount_type && $request->discount_value) {
                if ($request->discount_type === 'percent') {
                    $finalAmount -= ($total_amount_without_discount * $request->discount_value / 100);
                } else {
                    // خصم بقيمة ثابتة
                    $finalAmount -= $request->discount_value;
                }
            }
    
            // 1. إنشاء الفاتورة
            $invoice = CustomerInvoices::create([
                'customer_id' => $request->customer_id,
                'code' => $this->generateNum(),
                'date' => $request->date,
                'type' => $request->invoice_type,
                'total_amount_without_discount' => $total_amount_without_discount,
                'total_amount' => $total_amount,
                'total_profit' => $total_profit_inv,
                'cost_price' => $request->additional_cost,
                'paid_amount' => $finalAmount,
                'staute' => 1, 
                'notes' => $request->notes,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'user_id' => $this->user_id,
            ]);
    
            // 2. ضبط المخزن وخصم البضاعة وتسجيل عناصر الفاتورة
            $this->updateStock($request, $invoice);
    
            // 3. جمع بيانات الخزن والمحافظ الديناميكية
            $warehouses = [];
            if ($request->warehouses && is_array($request->warehouses)) {
                foreach ($request->warehouses as $wh) {
                    if (!empty($wh['warehouse_id']) && !empty($wh['wallet_id'])) {
                        $warehouses[] = [
                            'warehouse_id' => $wh['warehouse_id'],
                            'wallet_id'    => $wh['wallet_id']
                        ];
                    }
                }
            }
    
            // 4. توزيع المبلغ والربح على الخزن المحددة
            $count = count($warehouses);
            if ($count > 0) {
                $share = $count > 1 ? $finalAmount / $count : $finalAmount;
                $shareProfit = $count > 1 ? $total_profit_inv / $count : $total_profit_inv;
                foreach ($warehouses as $wh) {
                    $warehouseModel = Warehouse::find($wh['warehouse_id']);
                    if ($warehouseModel && $warehouseModel->account) {
                        Account_transactions::create([
                            'account_id'       => $warehouseModel->account->id,
                            'wallet_id'        => $wh['wallet_id'],
                            'direction'        => 'in',
                            'amount'           => $share,
                            'profit_amount'    => $shareProfit,
                            'transaction_type' => 'sale',
                            'related_type'     => Customer::class,
                            'related_id'       => $request->customer_id,
                            'description'      => $request->notes ?? 'تحصيل فاتورة مبيعات كاش',
                            'source_code'      => $invoice->code,
                            'date'             => $invoice->invoice_date,
                            'user_id'          => $this->user_id,
                        ]);
                    }
                }
            }
    
            // 5. تحديث التكاليف إن وجدت
            $this->updateCost($request, $invoice);
    
            // 6. توزيع الربحية
            $this->ProfitDistribution($invoice);
            
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
        }
    }
    
    
    

    protected function updateStock($request, $invoice){
        $invoice->items()->delete();
        $invoice_items = $request->input('items');
        foreach($invoice_items as $item){
            // 1. خصم من المخزن البضاعة 
            $stock = Stock::where('id', $item['stock_id'])->first();
            $stock->movements()->where('source_code', $invoice->code)->delete(); // حذف كل سجل الحركات لإعادة إنشاءه من جديد
            $quantity = $item['quantity'];

            if($stock){
                // 2. إنشاء سجل حركات جديد
                $stock->movements()->create([
                    'related_type' => Customer::class,
                    'related_id' => $request->customer_id,
                    'stock_id' => $stock->id,
                    'type' => 'out',
                    'quantity' => -$quantity,
                    'note' => 'بيع',
                    'source_code' => $invoice->code,
                    'date' => $request->date,
                    'user_id' => $this->user_id,
                ]);
   
                // 2. تسجيل عناصر الفاتورة 
                $invoice->items()->create([
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'unit_name' => $item['unit_name'],
                    'size'   => !empty($item['size']) ? $item['size'] : null,
                    'quantity' => $quantity,
                    'sale_price' => $this->normalizeNumber($item['sale_price']),
                    'total_price' => $this->normalizeNumber($item['total_price']),
                    'profit' => $this->normalizeNumber($item['profit']),
                    'total_profit' => $this->normalizeNumber($item['total_profit']),
                ]);
            }
        }
    }

    protected function updateInvoiceState($request)
    {
        $customer = Customer::findOrFail($request->customer_id);
    
        // مجموع المدفوعات المتاحة
        $available = $this->normalizeNumber(
            $customer->paymentTransactions()->sum('amount')
        );
    
        // معالجة الفواتير على دفعات لتجنب استهلاك الذاكرة
        CustomerInvoices::where('customer_id', $customer->id)
            ->where('type', '!=', 'cash')
            ->orderBy('date', 'asc')
            ->chunkById(500, function ($invoices) use (&$available, $request) {
    
                foreach ($invoices as $invoice) {
    
                    // المبلغ بعد الخصم
                    $invoiceAmount = $this->normalizeNumber($invoice->total_amount_without_discount);
                    if ($invoice->discount_type && $invoice->discount_value) {
                        if ($invoice->discount_type === 'percent') {
                            $invoiceAmount -= ($invoiceAmount * $invoice->discount_value / 100);
                        } else {
                            $invoiceAmount -= $invoice->discount_value;
                        }
                    }
    
                    // جلب المستحق الحالي إذا وجد
                    $due = $invoice->dues()->first();
    
                    // لا يوجد رصيد يغطي الفاتورة
                    if ($available <= 0) {
                        if (!$due) {
                            $invoice->dues()->create([
                                'customer_id' => $request->customer_id,
                                'customer_invoice_id' => $invoice->id,
                                'description' => 'مستحقات لنا',
                                'amount'      => $invoiceAmount,
                                'paid_amount' => 0,
                                'due_date'    => $invoice->date,
                                'status'      => 0,
                                'user_id'     => $this->user_id,
                            ]);
                        } else {
                            // تحديث إذا كان موجودًا
                            $due->update([
                                'amount'      => $invoiceAmount,
                                'paid_amount' => 0,
                                'status'      => 0,
                            ]);
                        }
                        $invoice->update(['paid_amount' => 0, 'staute' => 0]);
                        continue;
                    }
    
                    // الفاتورة مدفوعة بالكامل
                    if ($available >= $invoiceAmount) {
                        $invoice->update(['paid_amount' => $invoiceAmount, 'staute' => 1]);
                        if ($due) {
                            $due->delete();
                        }
                        $available -= $invoiceAmount;
                        continue;
                    }
    
                    // الفاتورة مدفوعة جزئيًا
                    if ($available > 0 && $available < $invoiceAmount) {
                        $paidNow = $available;
    
                        $invoice->update(['paid_amount' => $paidNow, 'staute' => 2]);
    
                        if ($due) {
                            $due->update([
                                'amount'      => $invoiceAmount,
                                'paid_amount' => $paidNow,
                                'status'      => 2,
                            ]);
                        } else {
                            $invoice->dues()->create([
                                'customer_id' => $request->customer_id,
                                'customer_invoice_id' => $invoice->id,
                                'description' => 'مستحقات لنا',
                                'amount'      => $invoiceAmount,
                                'paid_amount' => $paidNow,
                                'due_date'    => $invoice->date,
                                'status'      => 2,
                                'user_id'     => $this->user_id,
                            ]);
                        }
    
                        $available = 0;
                        continue;
                    }
                }
            });
    
        // إدارة الدين الخارجي
        if ($available > 0) {
            $customer->debts()->updateOrCreate(
                ['debtable_id' => $customer->id, 'debtable_type' => Customer::class],
                [
                    'description' => 'مستحقات زائدة للعميل',
                    'amount'      => $available,
                    'paid'        => 0,
                    'remaining'   => $available,
                    'is_paid'     => 0,
                    'date'        => now(),
                    'user_id'     => $this->user_id,
                ]
            );
        } else {
            $customer->debts()->delete();
        }
    }
    
    
    
    

    protected function ProfitDistribution($invoice)
    {
        // لو مفيش ربح أصلاً
        if (!$invoice->total_profit || $invoice->total_profit <= 0) {
            $profitItemIds = ExponseItem::where('is_profit', 1)
                ->pluck('id')
                ->toArray();
            $invoice->costs()->whereIn('expense_item_id', $profitItemIds)
                ->where('user_id', $this->user_id)
                ->delete();
            return;
        }
    
        // جلب كل بنود الربح
        $profitItems = ExponseItem::where('is_profit', 1)->get();
        if ($profitItems->isEmpty()) return;
    
        $profitItemIds = $profitItems->pluck('id')->toArray();
    
        // حساب صافي الربح بعد خصم التكاليف غير الربحية المرتبطة بالمستخدم الحالي
        $totalCosts = $invoice->costs()
            ->where('user_id', $this->user_id)
            ->whereNotIn('expense_item_id', $profitItemIds)
            ->sum('amount');
    
        $netProfit = $invoice->total_profit - $totalCosts;
    
        if ($netProfit <= 0) {
            $invoice->costs()
                ->whereIn('expense_item_id', $profitItemIds)
                ->where('user_id', $this->user_id)
                ->delete();
            return;
        }
    
        // حذف التوزيع القديم المرتبط بالمستخدم الحالي
        $invoice->costs()
            ->whereIn('expense_item_id', $profitItemIds)
            ->where('user_id', $this->user_id)
            ->delete();
    
        // تحديد نصيب كل بند
        $shareAmount = $netProfit / $profitItems->count();
    
        // إعادة إنشاء التوزيع
        foreach ($profitItems as $item) {
            $invoice->costs()->create([
                'expense_item_id' => $item->id,
                'amount'          => $this->normalizeNumber($shareAmount),
                'note'            => 'توزيع ربحية الفاتورة',
                'date'            => $invoice->date,
                'source_code'     => $invoice->code,
                'user_id'         => $this->user_id,
            ]);
        }
    }
    

    public function payment(Request $request){
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
        $amount = $this->normalizeNumber($request->amount);
        // تسجيل حركة معاملة في الخزنة
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'in',
            'wallet_id'     => $request->wallet_id,
            'amount'     => $amount,
            'transaction_type' => 'payment',
            'related_type' => Customer::class,  
            'related_id' => $request->customer_id,
            'description' => $request->description ?? 'دفع مقدمة من العميل',
            'user_id' => $this->user_id,
        ]);
        
        // تسجيل دفعة للعميل
        paymentTransaction::create([
            'related_type' => Customer::class,
            'related_id' => $request->customer_id,
            'direction' => 'in',
            'amount' => $amount,
            'payment_date' => now()->toDateString(),
            'wallet_id' => $request->wallet_id,
            'description' => $request->description ?? 'دفعة مقدمة',
            'user_id' => $this->user_id,
        ]);

        $this->updateInvoiceState($request);
    
        return back()->with('success', 'تم دفع الدفعة بنجاح');
    }  

    public function filter(Request $request){
        $query = CustomerInvoices::query();

        if ($request->filled('searchText')) {
            $searchText = $request->searchText;
    
            $query->where(function ($q) use ($searchText) {
                $q->where('code', 'like', '%' . $searchText . '%')
                  ->orWhereHas('customer', function($q2) use ($searchText) {
                      $q2->where('name', 'like', '%' . $searchText . '%');
                  });
            });
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $invoices_list = $query->orderBy('date', 'desc')->paginate(100);

        return view('customer.sales.invoice_table', ['invoices_list' => $invoices_list])->render();
    }

    public function filterByCustomer(Request $request)
    {
        $query = CustomerInvoices::query()
            ->where('customer_id', $request->customer_id); // شرط العميل ثابت
    
        if ($request->filled('searchCode')) {
            $query->where('code', $request->searchCode);
        }
    
        if ($request->filled('invoice_type')) {
            $query->where('type', $request->invoice_type);
        }
    
        if ($request->filled('invoice_staute')) {
            if ($request->invoice_staute === 'unpaid') {
                $query->where(function ($q) {
                    $q->where('staute', 0)
                      ->orWhere('staute', 2);
                });
            } else {
                $query->where('staute', $request->invoice_staute);
            }
        }
    
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
    
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
    
        $invoices_list = $query->orderBy('date', 'desc')->paginate(100);
    
        return view('customer.sales.invoice_table', [
            'invoices_list' => $invoices_list
        ])->render();
    }
    
    public function show($code){
        $invoice = CustomerInvoices::where('code', $code)->first();
        $nonProfitCosts = $invoice->costs()
        ->whereHas('expenseItem', fn($q) => $q->where('is_profit', 0))
        ->get();
        $app = App::latest()->first();
        return view('customer.sales.show', compact('invoice', 'app', 'nonProfitCosts'));
    }

    public function deleteInv(Request $request)
    {
        DB::beginTransaction();
        try {
            $invoice = CustomerInvoices::findOrFail($request->id);
            $customer = Customer::findOrFail($request->customer_id);
    
            if ($invoice->dues()->exists()) {
                // فاتورة آجل: حذف المستحقات فقط
                $invoice->dues()->delete();
            } else {
                // فاتورة كاش: عمل مرتجع لكل حركة خزنة مرتبطة بالفاتورة
                $transactions = Account_transactions::where('source_code', $invoice->code)->get(); // أو Transaction::where('source_code', $invoice->code)->get();
    
                foreach ($transactions as $trans) {
                    $trans->replicate()->fill([
                        'amount' => -$trans->amount,
                        'direction' => $trans->direction === 'in' ? 'out' : 'in',
                        'transaction_type' => 'refund',
                        'description' => 'مرتجع فاتورة بيع '.$invoice->code,
                        'date' => now(),
                    ])->save();
                }
            }
    
            // حذف حركة الستوك المتعلقة بالفاتورة
            Stock_movement::where('source_code', $invoice->code)->delete();
    
            // حذف الفاتورة نفسها
            $invoice->delete();
    
            DB::commit();
    
            return redirect()->route('customer.account.show', $customer->id)
                             ->with('success', 'تم عمل مرتجع للفاتورة بنجاح');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function returnedInvoices(){
        $invoices_list = CustomerInvoices::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(100);
        return view('customer.sales.returned', compact('invoices_list'));
    }

    public function download($id){
        $invoice = CustomerInvoices::findOrFail($id);
        $app = App::latest()->first();

        $html = view('customer.sales.invoice-pdf', compact('invoice', 'app'))->render();

        // إعداد mPDF بدعم RTL واللغة العربية
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Arial',
            'default_font_size' => 12
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('invoice_'.$invoice->code.'.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

}
