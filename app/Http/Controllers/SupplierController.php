<?php

namespace App\Http\Controllers;

use App\Exports\SuppliersDataExport;
use App\Exports\SuppliersTemplateExport;
use App\Http\Requests\Invoices\supplierInvoiceRequest;
use App\Http\Requests\PaymentInvoiceRequest;
use App\Imports\SuppliersImport;
use App\Models\Account;
use App\Models\Account_transactions;
use App\Models\App;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\StoreHouse;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use App\Models\Unit;
use App\Models\Wallet;
use App\Models\Wallet_movement;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class SupplierController extends Controller
{
    public function index(){
        $suppliers_list = Supplier::all();
        return view('suppliers.index', compact('suppliers_list'));
    }

    public function add(){
        return view('suppliers.add');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required'
        ],[
            'name.required' => 'يجب إدخال اسم المورد !'
        ]);
        try {
            $Supplier = new Supplier();
            $Supplier->name = $request->name;
            $Supplier->phone = $request->phone;
            $Supplier->busniess_name = $request->busniess_name;
            $Supplier->busniess_type = $request->busniess_type;
            $Supplier->whatsUp = $request->whatsUp;
            $Supplier->place = $request->place;
            $Supplier->notes = $request->notes;
            $Supplier->save();

            $Supplier->account()->create([
                'name'     => 'حساب المورد: ' . $Supplier->name,
                'type' => 'supplier',
                'total_capital_balance' => 0,
                'total_profit_balance' => 0,
            ]);

        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return redirect()->route('supplier.index')->with('success', 'تم إضافة مورد بنجاح');
    }

    public function edit($id){
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request){
        $request->validate([
            'name' => 'required'
        ],[
            'name.required' => 'يجب إدخال اسم المورد !'
        ]);
        try {
            $Supplier = Supplier::findOrFail($request->id);
            $Supplier->name = $request->name;
            $Supplier->phone = $request->phone;
            $Supplier->busniess_name = $request->busniess_name;
            $Supplier->busniess_type = $request->busniess_type;
            $Supplier->whatsUp = $request->whatsUp;
            $Supplier->place = $request->place;
            $Supplier->notes = $request->notes;
            $Supplier->save();

            // edit account name
            $Supplier->account()->update([
                'name' => 'حساب المورد: ' . $request->name,
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return redirect()->route('supplier.index')->with('success', 'تم تعديل بيانات المورد بنجاح');
    }

    public function downloadTemplate(){
        return Excel::download(new SuppliersTemplateExport, 'نموذج_الموردين.xlsx');
    }

    public function importSuppliers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new SuppliersImport, $request->file('file'));

        return back()->with('success', 'تم استيراد الموردين بنجاح');
    }

    public function exportData(Request $request){
        $ids = json_decode($request->recardsIds[0]); 

        if (empty($ids)) {
            return redirect()->back()->with('error', 'لم يتم تحديد موردين');
        }

        return Excel::download(new SuppliersDataExport($ids),'بيانات الموردين.xlsx');
    }

    public function profileShow($id){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['supplier'] = Supplier::findOrFail($id);
        $data['supplier_invoices'] = Supplier_invoice::where('supplier_id' , $id)->paginate(100);
        return view('suppliers.profile', $data);
    }

    public function InvoiceIndex(){
        $invoices_list = Supplier_invoice::orderBy('invoice_date', 'desc')->paginate(100);
        $warehouse_list = Warehouse::where('is_main', 0)->get();
        return view('suppliers.invoices.index', compact('invoices_list', 'warehouse_list'));
    }
    
    public function InvoiceAdd($id = null){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        if($id){
            $data['supplier'] = Supplier::findOrFail($id);
        }
        else {
            $data['suppliers_list'] = Supplier::all();
        }
        $data['main_categories'] = Category::whereNull('parent_id')->get();
        return view('suppliers.invoices.add', $data);
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

    

    public function InvoiceStore(supplierInvoiceRequest $request){
        // التحقق من الفاتورة لو هيا آجل ام كاش
        if($request->invoice_type === 'cash'){
            return $this->paymentCash($request);
        }
        else if ($request->invoice_type === 'credit') {
            return $this->paymentcredit($request);
        }
        else {
            return $this->addOpenBalance($request);
        }
    }

    protected function paymentcredit($request){
        $total_amount = $this->normalizeNumber($request->total_amount);
        $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
        // أولاً: المورد
        $supplier = Supplier::where('id', $request->supplier_id)->first();
        $supplier->account()->increment('current_balance', $total_amount_invoice);

        // ثانياً: خزنة التوريدات
        $toridat_warehouse = Warehouse::where('type', 'toridat')->first();
        $toridat_warehouse->account()->decrement('total_capital_balance', $total_amount);

        // 2. إنشاء الفاتورة 
        $invoice = Supplier_invoice::create([
            'supplier_id' => $request->supplier_id,
            'invoice_code' => $this->generateNum(),
            'invoice_date' => $request->invoice_date,
            'invoice_type' => $request->invoice_type,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $total_amount_invoice,
            'cost_price' => $request->additional_cost,
            'notes' => $request->notes,
        ]);

        // 3. إضافة تفاصيل التكاليف
        $costs = $request->input('costs');
        if ($costs && is_array($costs)) {
            foreach ($costs as $cost) {
                $invoice->costs()->create([
                    'description' => $cost['description'],
                    'amount' => $cost['amount'],
                ]);
            }
        }

        // ضبط المخزن

        $main_store = StoreHouse::latest()->first(); 

        // إدخال أصناف الفاتورة + إدخال ستوك جديد لكل صنف
        $invoivce_items = $request->input('items');
        if ($invoivce_items && is_array($invoivce_items)) {
            foreach ($invoivce_items as $item) {   
                $invoice->items()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'pricePerMeter' => round($item['pricePerMeter'], 2),
                    'length' => $item['length'],
                    'purchase_price' => $item['purchase_price'],
                    'total_price' => round($item['total_price'], 2),
                ]);

                // إضافة المنتج إلي المخزن لو لم يكن موجود وتحديث الكمية لو كان موجود
                $stock = Stock::where([
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                ])->first();

                $unit = Unit::findOrFail($item['unit_id']);

                if($unit->symbol === 'سم'){
                    $quantity = $item['length'] * $item['quantity'];
                }
                else {
                    $quantity = $item['quantity'];
                }

                
                if ($stock) {
                    // إذا المنتج موجود بالفعل في المخزن، قم بتحديث الكمية فقط
                    $stock->initial_quantity += $quantity;
                    $stock->remaining_quantity += $quantity;
                    $stock->save();
                } else {
                    // إذا المنتج جديد، قم بإنشائه
                    $stock = Stock::create([
                        'category_id' => $item['category_id'],
                        'product_id' => $item['product_id'],
                        'code' => $invoice->invoice_code,
                        'store_house_id' => $main_store->id,
                        'unit_id' => $item['unit_id'],
                        'initial_quantity' => $quantity,
                        'remaining_quantity' => $quantity,
                    ]);
                } 

                // تسجيل حركة مخزن
                $stock_movement = new Stock_movement();
                $stock_movement->supplier_id = $request->supplier_id;
                $stock_movement->stock_id = $stock->id;
                $stock_movement->type = 'in';
                $stock_movement->quantity = $quantity;
                $stock_movement->note = 'شراء';
                $stock_movement->save();
            }
        }
        return back()->with('success', 'تم إنشاء فاتورة مورد بنجاح');
    }

    protected function paymentCash($request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
        $total_amount = $this->normalizeNumber($request->total_amount);
        $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);

        // 1. ضبط الحسابات
        // التأكد من أن الرصيد الحالي أكبر من صفر
        if ($wallet->current_balance <= 0) {
            return back()->with('info', 'رصيد المحفظة يجب أن يكون أكبر من صفر .');
        }

        // التأكد من أن مبلغ الفاتورة لا يتخطي رصيد المحفظة 
        if ($wallet->current_balance < $request->total_amount) {
            return back()->with('info', 'رصيد المحفظة غير كافي لإجراء هذه العملية برجاء التحقق من الرصيد .');
        }

        // الخزنة الفرعية
        $warehouse->account()->decrement('total_capital_balance', $total_amount);
        $warehouse->account()->increment('total_capital_balance', $total_amount);
        $warehouse->account()->decrement('current_balance', $total_amount);
        
        // المحفظة
        $wallet->decrement('current_balance', $total_amount);

        // المورد
        $supplier->account()->increment('current_balance', $total_amount_invoice);
        $supplier->account()->increment('total_capital_balance', $total_amount_invoice);
        $supplier->account()->decrement('total_capital_balance', $total_amount_invoice);

        // 2. إنشاء الفاتورة 
        $invoice = Supplier_invoice::create([
            'supplier_id' => $request->supplier_id,
            'invoice_code' => $this->generateNum(),
            'invoice_date' => $request->invoice_date,
            'invoice_type' => $request->invoice_type,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $total_amount_invoice,
            'paid_amount' => $total_amount,
            'invoice_staute' => 1,
            'cost_price' => $request->additional_cost,
            'notes' => $request->notes,
            'warehouse_id' => $request->warehouse_id,
            'wallet_id' => $request->wallet_id,
        ]);

        // 3. إضافة تفاصيل التكاليف
        $costs = $request->input('costs');
        if (isset($costs) && is_array($costs)) {
            foreach ($costs as $cost) {
                $invoice->costs()->create([
                    'description' => $cost['description'],
                    'amount' => $cost['amount'],
                ]);
            }
        }

        // 4. تسجيل حركة حساب 
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'out',
            'code'  => $invoice->invoice_code,
            'method'     => $request->method,
            'amount'     => $total_amount,
            'transaction_type' => 'purchase',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->description
        ]);

        // 5. تسجيل حركة محفظة
        $wallet_movement = new Wallet_movement();
        $wallet_movement->wallet_id = $request->wallet_id;
        $wallet_movement->amount = $total_amount;
        $wallet_movement->direction = 'out';
        $wallet_movement->note = 'فاتورة شراء';
        $wallet_movement->source_code = $invoice->invoice_code;
        $wallet_movement->save();


        // ضبط المخزن

        $main_store = StoreHouse::latest()->first(); 

        // إدخال أصناف الفاتورة + إدخال ستوك جديد لكل صنف
        $invoivce_items = $request->input('items');
        if ($invoivce_items && is_array($invoivce_items)) {
            foreach ($invoivce_items as $item) {
                $invoice->items()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'pricePerMeter' => round($item['pricePerMeter'], 2),
                    'length' => $item['length'],
                    'purchase_price' => $item['purchase_price'],
                    'total_price' => round($item['total_price'], 2),
                ]);

                // إضافة المنتج إلي المخزن لو لم يكن موجود وتحديث الكمية لو كان موجود
                $stock = Stock::where([
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                ])->first();

                
                if ($stock) {
                    // إذا المنتج موجود بالفعل في المخزن، قم بتحديث الكمية فقط
                    $stock->initial_quantity += $item['quantity'];
                    $stock->remaining_quantity += $item['quantity'];
                    $stock->save();
                } else {
                    // إذا المنتج جديد، قم بإنشائه
                    $stock = Stock::create([
                        'category_id' => $item['category_id'],
                        'product_id' => $item['product_id'],
                        'code' => $invoice->invoice_code,
                        'store_house_id' => $main_store->id,
                        'unit_id' => $item['unit_id'],
                        'initial_quantity' => $item['quantity'],
                        'remaining_quantity' => $item['quantity'],
                    ]);
                } 

                // تسجيل حركة مخزن
                $stock_movement = new Stock_movement();
                $stock_movement->supplier_id = $request->supplier_id;
                $stock_movement->stock_id = $stock->id;
                $stock_movement->type = 'in';
                $stock_movement->quantity = $item['quantity'];
                $stock_movement->note = 'شراء';
                $stock_movement->save();
            }
        }
        return back()->with('success', 'تم إنشاء فاتورة مورد بنجاح');
    }

    protected function addOpenBalance($request){
        $total_amount_invoice = $this->normalizeNumber($request->opening_balance_value);
        // إنشاء الفاتورة 
        $invoice = Supplier_invoice::where(['supplier_id' => $request->supplier_id, 'invoice_type' => 'opening_balance'])->exists();
        if($invoice == 1){
            return back()->with('error', 'هذا المورد لديه رصيد افتتاحي من قبل');
        }
        else {
            Supplier_invoice::create([
                'supplier_id'  => $request->supplier_id,
                'invoice_code' => $this->generateNum(),
                'invoice_date' => $request->invoice_date,
                'invoice_type' => $request->invoice_type,
                'invoice_staute' => 0,
                'total_amount' => $total_amount_invoice,
                'total_amount_invoice' => $total_amount_invoice,
                'notes' => $request->notes,
            ]);
        }

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->account()->increment('current_balance', $request->opening_balance_value);

        $warhouse = Warehouse::where('type', 'toridat')->first();
        $warhouse->account()->decrement('total_capital_balance', $request->opening_balance_value);

        return redirect()->route('supplier.invoice.index')->with('success', 'تم عمل رصيد افتتاحي للمورد بنجاح');
    }

    protected function normalizeNumber($number) {
        // إذا كانت الفاصلة العشرية , فحولها إلى .
        if (strpos($number, ',') !== false && strpos($number, '.') !== false) {
            // في حال الإدخال مثل: "1.000.000,25"
            $number = str_replace('.', '', $number); // حذف نقاط الألوف
            $number = str_replace(',', '.', $number); // تحويل الفاصلة العشرية
        } elseif (strpos($number, ',') !== false) {
            // في حال الإدخال مثل: "1000000,25"
            $number = str_replace(',', '.', $number); // فقط تحويل العشرية
        } else {
            // في حال الإدخال مثل: "1,000,000.25"
            $number = str_replace(',', '', $number); // حذف الفواصل
        }
    
        return (float) $number;
    }
    
    public function InvoiceEdit($id){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['invoice'] = Supplier_invoice::findOrFail($id);
        $data['suppliers_list'] = Supplier::all();
        $data['finalCategories'] = Category::doesntHave('children')->get();
        $data['products'] = Product::with('category')->get();
        $data['units'] = Unit::all();
        $data['sizes'] = Size::all();
        return view('suppliers.invoices.edit', $data);
    }

    public function InvoiceUpdate(supplierInvoiceRequest $request)
    {
        $invoice = Supplier_invoice::findOrFail($request->id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $total_amount = $this->normalizeNumber($request->total_amount) ?? 0;
        $total_amount_old = $this->normalizeNumber($request->total_amount_old) ?? 0;
        $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice) ?? 0;
        $total_amount_invoice_old = $this->normalizeNumber($request->total_amount_invoice_old) ?? 0;
        // $opening_balance = $this->normalizeNumber($request->opening_balance) || 0;
        // $opening_balance_old = $this->normalizeNumber($request->opening_balance_old) || 0;
        $cost_price = $request->additional_cost ?? 0;

        // تحديث بيانات الفاتورة
        $invoice->update([
            'supplier_id' => $request->supplier_id,
            'invoice_date' => $request->invoice_date,
            'invoice_type' => $request->invoice_type,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $total_amount_invoice,
            'cost_price' => $cost_price,
            'notes' => $request->notes,
        ]);
        
        // ضبط الحسابات 
        if($request->invoice_type === 'opening_balance'){
            $supplier->account()->decrement('current_balance', $total_amount_old);
            $supplier->account()->increment('current_balance', $total_amount);

            $warhouse = Warehouse::where('type', 'toridat')->first();
            $warhouse->account()->increment('total_capital_balance', $total_amount_old);
            $warhouse->account()->decrement('total_capital_balance', $total_amount);

            return redirect()->route('supplier.invoice.index')->with('success', 'تم تعديل الرصيد الإفتتاحي للمورد بنجاح');
        }
        elseif ($request->invoice_type === 'credit') {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $supplier->account()->decrement('current_balance', $total_amount_invoice_old);
            $supplier->account()->increment('current_balance', $total_amount_invoice);

            $warehouse_toridat = Warehouse::where('type', 'toridat')->first();
            $warehouse_toridat->account()->increment('total_capital_balance', $total_amount_old);
            $warehouse_toridat->account()->decrement('total_capital_balance', $total_amount);    
        }
        else {
            $wallet = Wallet::findOrFail($request->wallet_id);
            $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
            // التأكد انه اختار محفظة وحساب خزنة 
            if(!$request->warehouse_id && !$request->wallet_id){
                return back()->with('error', 'يجب اختيار حساب خزنة ومحفظة');
            }
    
            // التأكد من أن مبلغ الفاتورة لا يتخطي قيمة الفاتورة
            if (($this->normalizeNumber($wallet->current_balance) + $total_amount_old) < $total_amount) {
                return back()->with('info', 'رصيد المحفظة غير كافي لإجراء هذه العملية برجاء التحقق من الرصيد .');
            }
            
            // الخزنة الفرعية
            $warehouse->account()->increment('current_balance', $total_amount_old);
            $warehouse->account()->decrement('current_balance', $total_amount);
            
            // المورد
            $supplier->account()->decrement('current_balance', $total_amount_old);
            $supplier->account()->increment('current_balance', $total_amount);
    
            // رصيد المحظة
            $wallet->increment('current_balance', $total_amount_old);
            $wallet->decrement('current_balance', $total_amount);

            // تعديل الحركة 
            $transaction = Account_transactions::where('code', $invoice->invoice_code)->first();
            $transaction->amount = $total_amount;
            $transaction->save();

            // تعديل حركة محفظة
            $wallet_movement = Wallet_movement::where('source_code', $invoice->invoice_code)->first();
            $wallet_movement->amount = $total_amount;
            $wallet_movement->save();
        }

          // استرجاع الأصناف القديمة وتأثيرها على المخزن
          $oldItems = $invoice->items;
          foreach ($oldItems as $oldItem) {
              $stock = Stock::where([
                  'category_id' => $oldItem->category_id,
                  'product_id' => $oldItem->product_id,
              ])->first();
      
              if ($stock) {
                  // خصم الكمية القديمة من المخزن
                  $stock->initial_quantity -= $oldItem->quantity;
                  $stock->remaining_quantity -= $oldItem->quantity;
                  $stock->save();
              }
      
              // حذف حركات المخزن المرتبطة بالصنف
              Stock_movement::where('stock_id', optional($stock)->id)
                            ->where('supplier_id', $invoice->supplier_id)
                            ->where('note', 'شراء')
                            ->delete();
          }
    

    
        // حذف الأصناف القديمة لإعادة إدخالها من جديد
        $invoice->items()->delete();
    
        // إدخال الأصناف الجديدة وتحديث المخزن
        $items = $request->input('items');
        $main_store = StoreHouse::latest()->first();
    
        if ($items && is_array($items)) {
            foreach ($items as $item) {
                
                $newItem = $invoice->items()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'pricePerMeter' => round($item['pricePerMeter'], 2),
                    'length' => $item['length'],
                    'purchase_price' => $item['purchase_price'],
                    'total_price' => round($item['total_price'], 2),
                ]);

                $unit = Unit::findOrFail($item['unit_id']);

                if($unit->symbol === 'سم'){
                    $quantity = $item['length'] * $item['quantity'];
                }
                else {
                    $quantity = $item['quantity'];
                }

    
                $stock = Stock::where([
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'store_house_id' => $main_store->id,
                ])->first();
    
                if ($stock) {
                    $stock->initial_quantity += $quantity;
                    $stock->remaining_quantity += $quantity;
                    $stock->save();
                } else {
                    $stock = Stock::create([
                        'category_id' => $item['category_id'],
                        'product_id' => $item['product_id'],
                        'code' => $invoice->invoice_code,
                        'store_house_id' => $main_store->id,
                        'unit_id' => $item['unit_id'],
                        'initial_quantity' => $quantity,
                        'remaining_quantity' => $quantity,
                    ]);
                }
    
                // تسجيل حركة مخزن جديدة
                Stock_movement::create([
                    'supplier_id' => $request->supplier_id,
                    'stock_id' => $stock->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'note' => 'شراء',
                ]);
            }
        }
    
        // تعديل التكاليف
        $costs = $request->input('costs');
        if (is_array($costs) && isset($costs)) {
            $existingIds = collect($costs)->pluck('id')->filter()->toArray();
            $invoice->costs()->whereNotIn('id', $existingIds)->delete();
    
            foreach ($costs as $cost) {
                if (!empty($cost['id'])) {
                    $invoice->costs()->where('id', $cost['id'])->update([
                        'description' => $cost['description'],
                        'amount' => $cost['amount'],
                    ]);
                } else {
                    $invoice->costs()->create([
                        'description' => $cost['description'],
                        'amount' => $cost['amount'],
                    ]);
                }
            }
        }
    
        return back()->with('success', 'تم تعديل فاتورة المورد بنجاح');
    }    

    public function payment(Request $request){

    }

    public function InvoiceDelete(Request $request){
        $invoice = Supplier_invoice::findOrFail($request->id);
        
        // stock delete 
        $stock = Stock::where('code', $invoice->invoice_code)->first();
        $stock->delete();

        // تحديث الحساب المالي 

        // أولاً: المورد
        $supplier = Supplier::where('id', $request->supplier_id)->first();
        $supplier->account()->decrement('total_capital_balance', $request->total_amount);

        // ثانياً:  خزنة التوريدات
        $toridat_warehouse = Warehouse::where('type', 'toridat')->first();
        $toridat_warehouse->account()->decrement('total_capital_balance', $request->total_amount);


        // invoice delete 
        $invoice->delete();
        return back()->with('success', 'تم حذف فاتورة المورد بنجاح');
    }

    public function InvoiceShow($code){
        $invoice = Supplier_invoice::with('supplier')->where('invoice_code', $code)->first();
        $app = App::latest()->first();
        return view('suppliers.invoices.show', compact('invoice', 'app'));
    }

    public function InvoiceDownload($id){
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

    public function paymentInvoice(PaymentInvoiceRequest $request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();

        // التأكد من أن الرصيد الحالي أكبر من صفر
        if ($wallet->current_balance <= 0) {
            return back()->with('info', 'رصيد المحفظة يجب أن يكون أكبر من صفر .');
        }

        // التأكد من أن المبلغ لا يتجاوز الرصيد المتاح
        if ($request->amount > $wallet->current_balance) {
            return back()->with('info', 'رصيد المحفظة غير كافي لإجراء هذه العملية برجاء التحقق من الرصيد المتوفر في المحفظة .');
        }

        // 1. تحديث الفاتورة
        $invoice = Supplier_invoice::findOrFail($request->id);
        $Remaining = ($request->total_amount - $request->amount);
        if($Remaining > 0)
        {
            $invoice->paid_amount = $request->amount;
            $invoice->invoice_staute = 2;
            $invoice->save();
        }
        elseif($Remaining < 0)
        {
            return back()->with('info', 'مطلوب دفع ' . $request->total_amount . 'فقط لا غير');
        }
        else {
            $invoice->paid_amount = $request->amount;
            $invoice->invoice_staute = 1;
            $invoice->save();
        }
        
        // 2. تسجيل حركة معاملات 
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'out',
            'code'  => $invoice->invoice_code,
            'method'     => $request->method,
            'amount'     => $request->amount,
            'transaction_type' => 'purchase',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->description
        ]);

        // 3. تحديث الحسابات المالية 

        // أولاً:حساب المورد
        $supplier->account()->decrement('total_capital_balance', $request->amount);

        // ثانياً: خزنة المستهدفة + حساب المحفظة 
        $warehouse->account()->increment('total_capital_balance', $request->amount);
        $warehouse->account()->decrement('current_balance', $request->amount);

        $wallet->decrement('current_balance', $request->amount);

        // 4. تسجيل حركة محفظة 
        $wallet_movement = new Wallet_movement();
        $wallet_movement->wallet_id = $request->wallet_id;
        $wallet_movement->amount = $request->amount;
        $wallet_movement->direction = 'out';
        $wallet_movement->note = 'فاتورة شراء';
        $wallet_movement->source_code = $invoice->invoice_code;
        $wallet_movement->save();
        return back()->with('success', 'تم الدفع وتغيير حالة الفاتورة بنجاح');
    }

    public function filterInvoices(Request $request){
        $query = Supplier_invoice::query();

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

}
