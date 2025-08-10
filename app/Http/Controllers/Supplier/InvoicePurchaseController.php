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
use App\Models\Product;
use App\Models\Size;
use App\Models\Stock;
use App\Models\Stock_movement;
use App\Models\StoreHouse;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use App\Models\Supplier_invoice_item;
use App\Models\Unit;
use App\Models\Wallet;
use App\Models\Wallet_movement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class InvoicePurchaseController extends Controller
{
    public function index(){
        $invoices_list = Supplier_invoice::orderBy('invoice_date', 'desc')->paginate(100);
        $warehouse_list = Warehouse::where('is_main', 0)->get();
        return view('suppliers.invoices.index', compact('invoices_list', 'warehouse_list'));
    }
    
    public function add($id = null){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        if($id){
            $data['supplier'] = Supplier::findOrFail($id);
        }
        else {
            $data['suppliers_list'] = Supplier::all();
        }
        $data['main_categories'] = Category::whereNull('parent_id')->get();
        $data['exponse_list'] = ExponseItem::all();
        return view('suppliers.invoices.add', $data);
    }


    public function store(supplierInvoiceRequest $request){
        // التحقق من الفاتورة لو هيا رصيد افتتاحي ام لا
        if($request->invoice_type === 'opening_balance'){
            return $this->addOpenBalance($request);
        }
        elseif($request->invoice_type === 'cash')
        {
            return $this->cash($request);
        }
        else {
            return $this->credit($request);
        }
    }

    protected function updateStock($request, $invoice){
        $invoice_items = $request->input('items');
        $costs = $request->input('costs');
        $main_store = StoreHouse::latest()->first(); 

        
        if($invoice_items && is_array($invoice_items)){
            $invoice->items()->delete();
            foreach($invoice_items as $index => $item){
                $invoice->items()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'pricePerMeter' => $item['pricePerMeter'],
                    'length' => $item['length'],
                    'purchase_price' => $item['purchase_price'],
                    'total_price' => $this->normalizeNumber($item['total_price']),
                ]);


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
                        'store_house_id' => $main_store->id,
                        'unit_id' => $item['unit_id'],
                        'initial_quantity' => $quantity,
                        'remaining_quantity' => $quantity,
                        'date' => $invoice->invoice_date,
                    ]);
                } 

                // 7. تسجيل حركة مخزن
                // البحث عن الحركة القديمة بناءً على رقم الفاتورة
                $stock_movement = Stock_movement::where('source_code', $invoice->invoice_code)
                ->where('stock_id', $stock->id)
                ->first();

                if ($stock_movement) {
                    // تعديل الحركة القديمة
                    $stock_movement->supplier_id = $request->supplier_id;
                    $stock_movement->type = 'in';
                    $stock_movement->quantity = $quantity;
                    $stock_movement->note = 'شراء (تعديل)';
                    $stock_movement->date = $invoice->invoice_date;
                    $stock_movement->save();
                } else {
                    // إنشاء حركة جديدة فقط إذا لم تكن موجودة
                    $stock_movement = new Stock_movement();
                    $stock_movement->supplier_id = $request->supplier_id;
                    $stock_movement->stock_id = $stock->id;
                    $stock_movement->type = 'in';
                    $stock_movement->quantity = $quantity;
                    $stock_movement->note = 'شراء';
                    $stock_movement->source_code = $invoice->invoice_code;
                    $stock_movement->date = $invoice->invoice_date;
                    $stock_movement->save();
                }


                // 8. حساب نصيب الصنف من التكاليف الإضافية
                if ($costs && is_array($costs)) {
                    $total_invoice_amount = floatval($request->total_amount_invoice) ?: 1;
                    $general_cost = floatval($request->additional_cost);
                    $item_total_price = floatval($item['total_price']);
    
                    $item_percentage = $item_total_price / $total_invoice_amount;
    
                    $cost_share = ($item_percentage * $general_cost) + $item_total_price;
    
                    InvoiceProductCost::updateOrCreate([
                        'stock_id' => $stock->id,
                    ], [
                        'base_cost' => floatval($item['purchase_price']),
                        'cost_share' => $this->normalizeNumber($cost_share),
                    ]);
                }

            }
        }
        return redirect()->route('supplier.account.show', $request->supplier_id)->with('success', 'تم إنشاء فاتورة مورد بنجاح');   
    }

    protected function credit($request){
        DB::beginTransaction();
        try {
            $total_amount = $this->normalizeNumber($request->total_amount);
            $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
            $cost_total = $this->normalizeNumber($request->additional_cost);
            $supplier = Supplier::where('id', $request->supplier_id)->first();
            $toridat_warehouse = Warehouse::where('type', 'toridat')->first();
            $cash_wallet = Wallet::where('method', 'cash')->first();
    
            // 1. إنشاء الفاتورة (ضعنا paid_amount افتراضياً 0 وحالة 2 غير مدفوعة جزئياً)
            $invoice = Supplier_invoice::create([
                'supplier_id' => $request->supplier_id,
                'invoice_code' => $this->generateNum(),
                'invoice_date' => $request->invoice_date,
                'invoice_type' => $request->invoice_type,
                'total_amount' => $total_amount,
                'total_amount_invoice' => $total_amount_invoice,
                'cost_price' => $request->additional_cost,
                'paid_amount' => 0,
                'invoice_staute' => 2, // 1 = مدفوعة، 2 = لم تُسدَّد كلياً/جزئياً
                'notes' => $request->notes,
            ]);
    
            // 2. إضافة التكاليف في البنود الصحيحة (كما في كودك)
            $costs = $request->input('costs');
            if ($costs && is_array($costs)) {
                foreach ($costs as $cost) {
                    $invoice->costs()->create([
                        'expense_item_id' => $cost['exponse_id'], 
                        'account_id'      => $toridat_warehouse->account->id,
                        'amount'          => $cost['amount'],
                        'note'            => 'تكاليف إضافية',
                        'date' => $invoice->invoice_date,
                    ]);
                }

                // إضافة تكاليف في الخزنة 
                Account_transactions::create([
                    'account_id' => $toridat_warehouse->account->id,
                    'direction'  => 'out',
                    'method'     => $cash_wallet->method,
                    'amount'     => -$cost_total,
                    'transaction_type' => 'expense',
                    'related_type' => Supplier_invoice::class,  
                    'related_id' => $invoice->id,
                    'description' => $request->notes ?? 'مصروفات فواتير',
                    'source_code' => $invoice->invoice_code,
                    'date' => $invoice->invoice_date,
                ]);
        
                // 5. تسجيل حركة محفظة
                $wallet_movement = new Wallet_movement();
                $wallet_movement->wallet_id = $cash_wallet->id;
                $wallet_movement->amount = -$cost_total;
                $wallet_movement->direction = 'out';
                $wallet_movement->note = 'مصروفات فواتير';
                $wallet_movement->source_code = $invoice->invoice_code;
                $wallet_movement->save();
        
            }
    
            // رصيد المورد الحالي (قد يكون سالباً)
            $currentBalance = floatval($supplier->account->current_balance ?? 0);
    
            if ($currentBalance >= 0) {
                // لا يوجد رصيد يغطي الفاتورة -> نسجل دين كامل
                $invoice->debts()->create([
                    'description'   => 'فاتورة شراء للمورد ' . $invoice->supplier->name,
                    'amount'        => $total_amount_invoice,
                    'paid'          => 0,
                    'remaining'     => $total_amount_invoice,
                    'is_paid'       => 0,
                    'date' => $invoice->invoice_date,
                ]);
                $invoice->update([
                    'paid_amount' => 0,
                    'invoice_staute' => 2
                ]);
            } else {
                // يوجد رصيد للمورد (قيمة سالبة في current_balance تعني أنه "له فلوس")
                $availableCredit = abs($currentBalance); // قيمة موجبة
                if ($availableCredit >= $total_amount_invoice) {
                    // الرصيد يكفي لتسديد الفاتورة بالكامل
                    $invoice->debts()->delete();
                    $invoice->update([
                        'paid_amount' => $total_amount_invoice,
                        'invoice_staute' => 1
                    ]);
                } else {
                    // الرصيد يغطي جزء من الفاتورة فقط
                    $paidFromCredit = $availableCredit;
                    $remaining = $total_amount_invoice - $paidFromCredit;
    
                    $invoice->debts()->create([
                        'description'   => 'فاتورة شراء للمورد ' . $invoice->supplier->name,
                        'amount'        => $total_amount_invoice,
                        'paid'          => $paidFromCredit,
                        'remaining'     => $remaining,
                        'is_paid'       => 0,
                        'date' => $invoice->invoice_date,
                    ]);
    
                    $invoice->update([
                        'paid_amount' => $paidFromCredit,
                        'invoice_staute' => 2
                    ]);
                }
            }
    
            // تحديث رصيد المورد: زيادة بمقدار قيمة الفاتورة (يعكس حالة الحساب صافي)
            $supplier->account->increment('current_balance', $total_amount_invoice);
    
            // 6. عمل ستوك المخزن 
            $this->updateStock($request, $invoice);
    
            DB::commit();
            return redirect()->route('supplier.account.show', $request->supplier_id)->with('success', 'تم إنشاء فاتورة مورد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
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

    protected function normalizeNumber($number)
    {
        // احذف كل الفواصل فقط
        $number = str_replace(',', '', $number);
    
        return floatval($number);
    }    

    protected function cash($request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();
        $total_amount = $this->normalizeNumber($request->total_amount);
        $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
        $current_balance = $this->normalizeNumber($request->current_balance);

        // التأكد من أن مبلغ الفاتورة لا يتخطي رصيد المحفظة 
        // if ($total_amount > $current_balance) {   
        //     return back()->with('info', 'رصيد المحفظة غير كافي لإجراء هذه العملية .');
        // }

        // الخزنة الفرعية
        $warehouse->account()->decrement('current_balance', $total_amount);
        
        // المحفظة
        $wallet->decrement('current_balance', $total_amount);

        // المورد
        $supplier->account()->increment('current_balance', $total_amount_invoice);
        $supplier->account()->decrement('current_balance', $total_amount_invoice);

        // 2. إنشاء الفاتورة 
        $invoice = Supplier_invoice::create([
            'supplier_id' => $request->supplier_id,
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

        // 2. إضافة التكاليف في البنود الصحيحة
        $costs = $request->input('costs');
         if ($costs && is_array($costs)) {
             foreach ($costs as $cost) {
                 $invoice->costs()->create([
                     'expense_item_id' => $cost['exponse_id'],
                     'account_id'      => $warehouse->account->id,
                     'amount'          => $cost['amount'],
                     'note'            => 'تكاليف إضافية',
                     'date' => $invoice->invoice_date,
                 ]);
             }
         }

        // 4. تسجيل حركة حساب 
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'out',
            'method'     => $request->method,
            'amount'     => -$total_amount,
            'transaction_type' => 'purchase',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->notes ?? 'عملية دفع فاتورة كاش',
            'source_code' => $invoice->invoice_code,
            'date' => $invoice->invoice_date,
        ]);

        // 5. تسجيل حركة محفظة
        $wallet_movement = new Wallet_movement();
        $wallet_movement->wallet_id = $request->wallet_id;
        $wallet_movement->amount = -$total_amount;
        $wallet_movement->direction = 'out';
        $wallet_movement->note = 'فاتورة شراء';
        $wallet_movement->source_code = $invoice->invoice_code;
        $wallet_movement->save();

        // ضبط المخزن
        $this->updateStock($request, $invoice);

        return redirect()->route('supplier.account.show', $request->supplier_id)->with('success', 'تم إنشاء فاتورة مورد بنجاح');
    }

    protected function addOpenBalance($request){
        $total_amount_invoice = $this->normalizeNumber($request->opening_balance_value);

        // تأكد أنه لا يوجد رصيد افتتاحي سابق
        $exists = Supplier_invoice::where([
            'supplier_id' => $request->supplier_id, 
            'invoice_type' => 'opening_balance'
        ])->exists();
    
        if ($exists) {
            return back()->with('error', 'هذا المورد لديه رصيد افتتاحي من قبل');
        }
    
        // إنشاء الفاتورة
        $invoice = Supplier_invoice::create([
            'supplier_id'         => $request->supplier_id,
            'invoice_code'        => $this->generateNum(),
            'invoice_date'        => $request->invoice_date,
            'invoice_type'        => $request->invoice_type,
            'invoice_staute'      => 0,
            'total_amount'        => $total_amount_invoice,
            'total_amount_invoice'=> $total_amount_invoice,
            'notes'               => $request->notes,
        ]);

        // تسجيلها كـ دين خارجي
        ExternalDebts::create([
            'debtable_type' => Supplier_invoice::class,
            'debtable_id'   => $invoice->id,
            'description'   => 'رصيد افتتاحي للمورد ' . $invoice->supplier->name,
            'amount'        => $total_amount_invoice,
            'paid'          => 0,
            'remaining'     => $total_amount_invoice,
            'is_paid'       => 0,
            'date'          => $invoice->invoice_date,
        ]);

        // تحديث حساب المورد
        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->account()->increment('current_balance', $request->opening_balance_value);
        
        return redirect()->route('supplier.index')->with('success', 'تم عمل رصيد افتتاحي للمورد بنجاح');
    }

    protected function updateOpenBalance($request)
    {
        $invoice = Supplier_invoice::findOrFail($request->id);
        $supplier = Supplier::findOrFail($invoice->supplier_id);
    
        $oldAmount = $this->normalizeNumber($request->opening_balance_old);
        $newAmount = $this->normalizeNumber($request->opening_balance);
    
        // تحديث بيانات الفاتورة
        $invoice->update([
            'invoice_date'         => $request->invoice_date,
            'total_amount'         => $newAmount,
            'total_amount_invoice' => $newAmount,
            'notes'                => $request->notes,
        ]);
    
        $paid = $invoice->paid_amount ?? 0;
        $remaining = max($newAmount - $paid, 0);
    
        // لو فيه دين مرتبط
        if ($invoice->debts) {
            $debt = $invoice->debts;
    
            if ($paid >= $newAmount) {
                // الدين تم دفعه بالكامل → نحذفه
                $debt->delete();
            } else {
                // تحديث بيانات الدين
                $debt->update([
                    'amount'    => $newAmount,
                    'paid'      => min($paid, $newAmount),
                    'remaining' => $remaining,
                    'is_paid'   => 0,
                    'date' => $invoice->invoice_date,
                ]);
            }
        } else {
            // لا يوجد دين حالياً، ننشئه فقط إذا لم يُدفع المبلغ بالكامل
            if ($paid < $newAmount) {
                ExternalDebts::create([
                    'debtable_type' => Supplier_invoice::class,
                    'debtable_id'   => $invoice->id,
                    'description'   => 'رصيد افتتاحي للمورد ' . $supplier->name,
                    'amount'        => $newAmount,
                    'paid'          => $paid,
                    'remaining'     => $remaining,
                    'is_paid'       => 0,
                    'date' => $invoice->invoice_date,
                ]);
            }
        }
    
        // تحديث حالة الفاتورة
        if ($paid == 0) {
            $invoice->update(['invoice_staute' => 0]); // غير مدفوعة
        } elseif ($paid >= $newAmount) {
            $invoice->update(['invoice_staute' => 1]); // مدفوعة بالكامل
        } else {
            $invoice->update(['invoice_staute' => 2]); // مدفوعة جزئياً
        }
    
        // 3. ضبط حساب المورد
        $all_paid = Supplier_invoice::where('supplier_id', $request->supplier_id)->sum('paid_amount');
        $all_amount_invoice = Supplier_invoice::where('supplier_id', $request->supplier_id)->sum('total_amount_invoice');

        $supplier_account = ($all_amount_invoice - $all_paid);

        $supplier->account()->update([
            'current_balance' => $supplier_account
        ]);
    
        return redirect()->route('supplier.index')->with('success', 'تم تعديل الرصيد الافتتاحي بنجاح مع ضبط الديون والرصيد.');
    }

    protected function updateCredit($request){
        $invoice = Supplier_invoice::findOrFail($request->id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $cost_total = $this->normalizeNumber($request->additional_cost);
        $paid = $this->normalizeNumber($invoice->paid_amount);
        $newAmount = $this->normalizeNumber($request->total_amount_invoice);
        $total_amount = $this->normalizeNumber($request->total_amount);
        $remaining = max($newAmount - $paid, 0);
        $diff = $this->normalizeNumber($request->total_amount_invoice) - $paid;

        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $newAmount,
            'cost_price' => $request->additional_cost,
            'notes' => $request->notes,
        ]);
    
        // 1. في حالة ان الفاتورة لم يتم دفعها جزئي أو لم يتم دفع اى قيمة 
        if($invoice->invoice_staute != 1){
            // ارفع المديونية 
            if($invoice->debts){
                $debt = $invoice->debts;
                if ($paid >= $newAmount) {
                    // الدين تم دفعه بالكامل → نحذفه
                    $debt->delete();
                    $invoice->update([
                        'invoice_staute' => 1,
                    ]);

                } else {
                    // تحديث بيانات الدين
                    $debt->update([
                        'amount'    => $newAmount,
                        'paid'      => min($paid, $newAmount),
                        'remaining' => $remaining,
                        'is_paid'   => 0,
                        'date' => $invoice->invoice_date,
                    ]);
                    $invoice->update([
                        'invoice_staute' => 0,
                    ]);
                }
            }
        }
        else {
            if($paid < $newAmount){
                ExternalDebts::create([
                    'debtable_type' => Supplier_invoice::class,
                    'debtable_id'   => $invoice->id,
                    'description'   => 'فاتورة شراء للمورد ' . $supplier->name,
                    'amount'        => $newAmount,
                    'paid'          => $diff,
                    'remaining'     => $diff,
                    'is_paid'       => 0,
                    'date' => $invoice->invoice_date,
                ]);

                $invoice->update([
                    'paid_amount' => $diff,
                ]);
            }
            else {
                $invoice->update([
                    'paid_amount' => $invoice->total_amount_invoice,
                ]);
                $invoice->debt->delete();
            }
        }

        // 2. تعديل حركة الخزنة لضبط الرصيد عند تغيير التكاليف
        $invoice->transaction()->update([
            'amount' => -$cost_total
        ]);

        
        // تحديث التكاليف الإضافية
        $costs = $request->input('costs');
        if ($costs && is_array($costs)) {
            foreach ($costs as $cost) {
                $exponse = Exponse::where('expense_item_id', $cost['exponse_id'])->first();
                $exponse->amount = $cost['amount'];
                $exponse->save();
            }
        }
     

        // تحديث حالة الفاتورة

        if ($paid == 0) {
            $invoice->update(['invoice_staute' => 0]); // غير مدفوعة
        } elseif ($paid >= $newAmount) {
            $invoice->update(['invoice_staute' => 1]); // مدفوعة بالكامل
        } else {
            $invoice->update(['invoice_staute' => 2]); // مدفوعة جزئياً
        }

        // 3. ضبط حساب المورد
        $all_paid = Supplier_invoice::where('supplier_id', $request->supplier_id)->sum('paid_amount');
        $all_amount_invoice = Supplier_invoice::where('supplier_id', $request->supplier_id)->sum('total_amount_invoice');

        $supplier_account = ($all_amount_invoice - $all_paid);

        $supplier->account()->update([
            'current_balance' => $supplier_account
        ]);


        // 4. ضبط ستوك المخزن
        $this->updateStock($request, $invoice);

        return redirect()->route('supplier.index')->with('success', 'تم تعديل الرصيد الافتتاحي بنجاح مع ضبط الديون والرصيد.');
    }

    protected function updateCash($request){
        $total_amount = $this->normalizeNumber($request->total_amount);
        $newAmount = $this->normalizeNumber($request->total_amount_invoice);
        $invoice = Supplier_invoice::findOrFail($request->id);

        // تحديث التكاليف الإضافية
        $costs = $request->input('costs');
        if ($costs && is_array($costs)) {
            foreach ($costs as $cost) {
                $exponse = Exponse::where('expense_item_id', $cost['exponse_id'])->first();
                $exponse->amount = $cost['amount'];
                $exponse->save();
            }
        }

        Account_transactions::where('source_code', $invoice->invoice_code)->update([
            'amount' => -$total_amount
        ]);

        Wallet_movement::where('source_code', $invoice->invoice_code)->update([
            'amount' => -$total_amount
        ]);

        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'total_amount' => $total_amount,
            'total_amount_invoice' => $newAmount,
            'paid_amount' => $newAmount,
            'cost_price' => $request->additional_cost,
            'notes' => $request->notes,
        ]);

        $this->updateStock($request, $invoice);
        return redirect()->route('supplier.index')->with('success', 'تم تعديل فاتورة المورد بنجاح');
    }
    
    public function edit($id){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['invoice'] = Supplier_invoice::findOrFail($id);
        $data['suppliers_list'] = Supplier::all();
        $data['finalCategories'] = Category::doesntHave('children')->get();
        $data['products'] = Product::with('category')->get();
        $data['units'] = Unit::all();
        $data['sizes'] = Size::all();
        $data['exponse_list'] = ExponseItem::all();
        return view('suppliers.invoices.edit', $data);
    }

    public function update(supplierInvoiceRequest $request)
    {
        if($request->invoice_type === 'opening_balance'){
            return $this->updateOpenBalance($request);
        }
        elseif($request->invoice_type === 'credit'){
            return $this->updateCredit($request);
        }
        else {
            return $this->updateCash($request);
        }
        return back()->with('error', 'حدث خطأ ما برجاء التواصل مع الدعم للمساعدة');
    }    

    public function delete(Request $request){
        $invoice = Supplier_invoice::findOrFail($request->id);
        
        // stock delete 
        $stock = Stock::where('code', $invoice->invoice_code)->first();
        $stock->delete();

        // تحديث الحساب المالي 

        // أولاً: المورد
        $supplier = Supplier::where('id', $request->supplier_id)->first();

        // ثانياً:  خزنة التوريدات
        $toridat_warehouse = Warehouse::where('type', 'toridat')->first();


        // invoice delete 
        $invoice->delete();
        return back()->with('success', 'تم حذف فاتورة المورد بنجاح');
    }

    public function show($code){
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
        $wallet = Wallet::findOrFail($request->wallet_id);
        $supplier = Supplier::findOrFail($request->supplier_id);
        $warehouse = Warehouse::where('id', $request->warehouse_id)->first();

        $amount = $this->normalizeNumber($request->amount);

        // تحديث الفواتير تلقائياً عند دفع دفعة مقدمة 
        $invoices = Supplier_invoice::where('supplier_id', $request->supplier_id)
                    ->where('invoice_staute', '!=', 1) // تجاهل الفواتير المدفوعة
                    ->orderBy('invoice_date', 'asc') // ترتيب حسب الأقدمية
                    ->get();

        foreach ($invoices as $inv) {
            $remaining = $inv->total_amount_invoice - $inv->paid_amount;
        
            if ($amount >= $remaining) {
                // دفع الفاتورة بالكامل
                $inv->update([
                    'invoice_staute' => 1,
                    'paid_amount' => $inv->paid_amount + $remaining,
                ]);
        
                // حذف الدين الخارجي
                if ($inv->debts) {
                    $inv->debts->delete();
                }
        
                $amount -= $remaining;
        
            } elseif ($amount > 0) {
                // دفع جزئي
                $inv->update([
                    'invoice_staute' => 2,
                    'paid_amount' => $inv->paid_amount + $amount,
                ]);
        
                if ($inv->debts) {
                    $inv->debts->update([
                        'paid' => $inv->debts->paid + $amount,
                        'remaining' => $inv->debts->amount - ($inv->debts->paid + $amount),
                        'is_paid' => 0
                    ]);
                }
        
                $amount = 0;
                break; // انتهى المبلغ
            } else {
                break;
            }
        }
        

        $amount = $this->normalizeNumber($request->amount);

        // تسجيل حركة محفظة 
        $wallet_movement = new Wallet_movement();
        $wallet_movement->wallet_id = $request->wallet_id;
        $wallet_movement->amount = -$amount;
        $wallet_movement->direction = 'out';
        $wallet_movement->note = 'دفعة';
        $wallet_movement->save();

        // تسجيل حركة معاملة في الخزنة
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction'  => 'out',
            'method'     => $request->method,
            'amount'     => -$amount,
            'transaction_type' => 'payment',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->description
        ]);

        $transaction = Account_transactions::where('account_id', $warehouse->account->id)->sum('amount');
        $all_wallet_movement = Wallet_movement::where('wallet_id', $request->wallet_id)->sum('amount');

        // الخزنة
        $warehouse->account->update([
            'current_balance' => $transaction
        ]);

        // المحفظة 
        $wallet->update([
            'current_balance' => $all_wallet_movement
        ]);

        // المورد
        $supplier->account()->decrement('current_balance', $amount);    

        return back()->with('success', 'تم دفع الدفعة بنجاح');
    }

    public function filter(Request $request){
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
