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

    protected function updateStock($request, $invoice)
    {
        $invoice_items = $request->input('items');
        $costs = $request->input('costs');
        $main_store = StoreHouse::latest()->first();

        if ($invoice_items && is_array($invoice_items)) {
            // 1. استرجاع الأصناف القديمة
            $old_items = $invoice->items;

            // 2. طرح الكميات القديمة من المخزون
            foreach ($old_items as $old_item) {
                $stock = Stock::where([
                    'category_id' => $old_item->category_id,
                    'product_id' => $old_item->product_id,
                ])->first();

                if ($stock) {
                    // حساب كمية حسب الوحدة (إذا سم مثلا)
                    $unit = Unit::find($old_item->unit_id);
                    $quantity_to_deduct = ($unit && $unit->symbol === 'سم') ? ($old_item->length * $old_item->quantity) : $old_item->quantity;

                    $stock->initial_quantity -= $quantity_to_deduct;
                    $stock->remaining_quantity -= $quantity_to_deduct;

                    // لا تجعل الكمية أقل من صفر
                    $stock->initial_quantity = max($stock->initial_quantity, 0);
                    $stock->remaining_quantity = max($stock->remaining_quantity, 0);

                    $stock->save();
                }
            }

            // 3. حذف الأصناف القديمة
            $invoice->items()->delete();

            // 4. إضافة الأصناف الجديدة وتحديث المخزون
            foreach ($invoice_items as $index => $item) {
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

                // تحديث المخزون
                $stock = Stock::where([
                    'category_id' => $item['category_id'],
                    'product_id' => $item['product_id'],
                ])->first();

                $unit = Unit::findOrFail($item['unit_id']);
                $quantity_to_add = ($unit->symbol === 'سم') ? ($item['length'] * $item['quantity']) : $item['quantity'];

                if ($stock) {
                    $stock->initial_quantity += $quantity_to_add;
                    $stock->remaining_quantity += $quantity_to_add;
                    $stock->save();
                } else {
                    $stock = Stock::create([
                        'category_id' => $item['category_id'],
                        'product_id' => $item['product_id'],
                        'store_house_id' => $main_store->id,
                        'unit_id' => $item['unit_id'],
                        'initial_quantity' => $quantity_to_add,
                        'remaining_quantity' => $quantity_to_add,
                        'date' => $invoice->invoice_date,
                    ]);
                }

                // تسجيل أو تحديث حركة المخزن
                $stock_movement = Stock_movement::where('source_code', $invoice->invoice_code)
                    ->where('stock_id', $stock->id)
                    ->first();

                if ($stock_movement) {
                    $stock_movement->update([
                        'supplier_id' => $request->supplier_id,
                        'type' => 'in',
                        'quantity' => $quantity_to_add,
                        'note' => 'شراء',
                        'date' => $invoice->invoice_date,
                    ]);
                } else {
                    Stock_movement::create([
                        'supplier_id' => $request->supplier_id,
                        'stock_id' => $stock->id,
                        'type' => 'in',
                        'quantity' => $quantity_to_add,
                        'note' => 'شراء',
                        'source_code' => $invoice->invoice_code,
                        'date' => $invoice->invoice_date,
                    ]);
                }

            // حساب نصيب الصنف من التكاليف الإضافية
            if ($costs && is_array($costs) && $request->additional_cost > 0) {
                $total_invoice_amount = $this->normalizeNumber($request->total_amount_invoice);
                $general_cost = $this->normalizeNumber($request->additional_cost);
                $item_total_price = $this->normalizeNumber($item['total_price']);

                $item_percentage = $item_total_price / $total_invoice_amount;

                $cost_share = ($item_percentage * $general_cost) + $item_total_price;

                InvoiceProductCost::updateOrCreate([
                    'stock_id' => $stock->id,
                ], [
                    'base_cost' => $this->normalizeNumber($item['total_price']),
                    'cost_share' => $this->normalizeNumber($cost_share),
                ]);
            } else {
                // إذا لا يوجد تكاليف إضافية، نعتمد سعر الشراء كسعر التكلفة
                InvoiceProductCost::updateOrCreate([
                    'stock_id' => $stock->id,
                ], [
                    'base_cost' => floatval($item['purchase_price']),
                    'cost_share' => floatval($item['purchase_price']),
                ]);
            }

            }
        }
    }

    protected function credit($request){
        DB::beginTransaction();
        try {
            $total_amount_invoice = $this->normalizeNumber($request->total_amount_invoice);
            $supplier = Supplier::findOrFail($request->supplier_id);
            $toridat_warehouse = Warehouse::where('type', 'toridat')->first();
            $cash_wallet = Wallet::where('method', 'cash')->first();
    
            // 1) إنشاء الفاتورة الجديدة بدون دفع
            $invoice = Supplier_invoice::create([
                'supplier_id' => $supplier->id,
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
    
            // 2) إضافة التكاليف وحركات الخزنة (إن وجدت)
            $costs = $request->input('costs');
            if ($costs && is_array($costs)) {
                foreach ($costs as $cost) {
                    $invoice->costs()->create([
                        'expense_item_id' => $cost['exponse_id'],
                        'account_id' => $toridat_warehouse->account->id,
                        'amount' => $this->normalizeNumber($cost['amount']),
                        'note' => 'تكاليف إضافية',
                        'date' => $invoice->invoice_date,
                    ]);
                }
                Account_transactions::create([
                    'account_id' => $toridat_warehouse->account->id,
                    'direction' => 'out',
                    'method' => $cash_wallet->method,
                    'amount' => -$this->normalizeNumber($request->additional_cost),
                    'transaction_type' => 'expense',
                    'related_type' => Supplier_invoice::class,
                    'related_id' => $invoice->id,
                    'description' => $request->notes ?? 'مصروفات فواتير',
                    'source_code' => $invoice->invoice_code,
                    'date' => $invoice->invoice_date,
                ]);
                Wallet_movement::create([
                    'wallet_id' => $cash_wallet->id,
                    'amount' => -$this->normalizeNumber($request->additional_cost),
                    'direction' => 'out',
                    'note' => 'مصروفات فواتير',
                    'source_code' => $invoice->invoice_code,
                ]);
            }
    
            // 3) احصل على جميع الفواتير السابقة (مرتبة زمنياً)، تشمل الفاتورة الجديدة
            $totalPayments = $supplier->paymentTransactions()->sum(DB::raw('ABS(amount)'));

            $fullyPaidInvoicesSum = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', 1)
                ->sum('total_amount_invoice');
            if($supplier->account->current_balance < 0){
    
                $remainingPayments = $totalPayments - $fullyPaidInvoicesSum;
                if ($remainingPayments < 0) $remainingPayments = 0;
    
                $unpaidInvoices = Supplier_invoice::where('supplier_id', $supplier->id)
                    ->where('invoice_staute', '!=', 1)
                    ->orderBy('invoice_date')
                    ->orderBy('id')
                    ->get();
    
                foreach ($unpaidInvoices as $invoice) {
                    $remainingInvoice = $invoice->total_amount_invoice - $invoice->paid_amount;
                
                    if ($remainingPayments >= $remainingInvoice) {
                        // دفع كامل الفاتورة
                        $invoice->update([
                            'paid_amount' => $invoice->total_amount_invoice,
                            'invoice_staute' => 1,
                        ]);
                        // حذف أي دين مرتبط
                        if ($invoice->debts) {
                            $invoice->debts->delete();
                        }
                        $remainingPayments -= $remainingInvoice;
                    } elseif ($remainingPayments > 0) {
                        // دفع جزئي
                        $newPaidAmount = $invoice->paid_amount + $remainingPayments;
                        $invoice->update([
                            'paid_amount' => $newPaidAmount,
                            'invoice_staute' => 2,
                        ]);
                        // تحديث أو إنشاء الدين
                        if ($invoice->debts) {
                            $invoice->debts->update([
                                'paid' => $newPaidAmount,
                                'remaining' => $invoice->total_amount_invoice - $newPaidAmount,
                                'is_paid' => 0,
                            ]);
                        } else {
                            $invoice->debts()->create([
                                'description' => 'دين جزئي على الفاتورة',
                                'amount' => $invoice->total_amount_invoice,
                                'paid' => $newPaidAmount,
                                'remaining' => $invoice->total_amount_invoice - $newPaidAmount,
                                'is_paid' => 0,
                                'date' => $invoice->invoice_date,
                            ]);
                        }
                
                        $remainingPayments = 0;
                        break;
                    } else {
                        // لم يتم دفع أي مبلغ على هذه الفاتورة
                        if (!$invoice->debts) {
                            $invoice->debts()->create([
                                'description' => 'دين كامل على الفاتورة',
                                'amount' => $invoice->total_amount_invoice,
                                'paid' => 0,
                                'remaining' => $invoice->total_amount_invoice,
                                'is_paid' => 0,
                                'date' => $invoice->invoice_date,
                            ]);
                        }
                        break;
                    }
                }
            }
            else {
                $invoice->debts()->create([
                    'description' => 'دين على الفاتورة',
                    'amount' => $invoice->total_amount_invoice,
                    'paid' => 0,
                    'remaining' => $invoice->total_amount_invoice,
                    'is_paid' => 0,
                    'date' => $invoice->invoice_date,
                ]);
            }
                

            // تحديث رصيد المورد النهائي
            $totalInvoicesSum = Supplier_invoice::where('supplier_id', $supplier->id)->sum('total_amount_invoice');

            $newBalance = $totalInvoicesSum - $totalPayments;

            $supplier->account()->update([
                'current_balance' => $this->normalizeNumber($newBalance),
            ]);                    
    
            // 7) تحديث المخزون
            $this->updateStock($request, $invoice);
    
            DB::commit();
    
            return redirect()->route('supplier.account.show', $supplier->id)
                ->with('success', 'تم إنشاء الفاتورة وتحديث الرصيد بنجاح.');
    
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

    protected function normalizeNumber($number)
    {
        if (is_null($number) || $number === '') {
            return 0;
        }
    
        // احذف الفواصل والمسافات
        $number = preg_replace('/[^\d.\-]/', '', $number);
    
        return is_numeric($number) ? floatval($number) : 0;
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


        $totalPaid = $supplier->paymentTransactions()
        ->get()
        ->sum(function ($payment) {
            return abs($payment->amount);
        });

        // تحديث رصيد المورد
        $totalInvoicesSum = Supplier_invoice::where('supplier_id', $supplier->id)->sum('total_amount_invoice');

        $newBalance = $totalInvoicesSum - $totalPaid;

        $supplier->account()->update([
            'current_balance' => $newBalance,
        ]);

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

    protected function addOpenBalance($request)
    {
        DB::beginTransaction();
        try {
            $total_amount_invoice = $this->normalizeNumber($request->opening_balance_value);

            // تأكد أنه لا يوجد رصيد افتتاحي سابق
            $exists = Supplier_invoice::where([
                'supplier_id' => $request->supplier_id,
                'invoice_type' => 'opening_balance'
            ])->exists();

            if ($exists) {
                return back()->with('error', 'هذا المورد لديه رصيد افتتاحي من قبل');
            }

            // إنشاء الفاتورة (رصيد افتتاحي)
            $invoice = Supplier_invoice::create([
                'supplier_id' => $request->supplier_id,
                'invoice_code' => $this->generateNum(),
                'invoice_date' => $request->invoice_date,
                'invoice_type' => $request->invoice_type,
                'invoice_staute' => 0, // غير مدفوعة مبدئياً
                'total_amount' => $total_amount_invoice,
                'total_amount_invoice' => $total_amount_invoice,
                'paid_amount' => 0,
                'notes' => $request->notes,
            ]);

            $supplier = Supplier::findOrFail($request->supplier_id);

            // تحديث حساب المورد (زيادة الرصيد الافتتاحي)
            $supplier->account()->increment('current_balance', $total_amount_invoice);

            // 1. جمع كل المدفوعات المقدمة للمورد (كمبلغ إيجابي)
            $totalPaid = $supplier->paymentTransactions()
                ->get()
                ->sum(function ($payment) {
                    return abs($payment->amount);
                });

            // 2. مجموع الفواتير المدفوعة بالكامل
            $fullyPaidInvoicesTotal = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', 1)
                ->sum('total_amount_invoice');

            // 3. المبلغ المتاح لتوزيعه على الفواتير غير المدفوعة
            $availableToPay = $totalPaid - $fullyPaidInvoicesTotal;

            // 4. جلب كل الفواتير الغير مدفوعة أو المدفوعة جزئياً بالترتيب
            $unpaidInvoices = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', '!=', 1)
                ->orderBy('invoice_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // 5. حذف ديون قديمة لجميع الفواتير الغير مدفوعة (احترازي)
            foreach ($unpaidInvoices as $inv) {
                $inv->debts()->delete();
            }

            // 6. توزيع المبلغ المتاح على الفواتير غير المدفوعة
            foreach ($unpaidInvoices as $inv) {
                $invoiceAmount = $this->normalizeNumber($inv->total_amount_invoice);

                if ($availableToPay >= $invoiceAmount) {
                    // دفع كامل للفاتورة
                    $inv->update([
                        'paid_amount' => $invoiceAmount,
                        'invoice_staute' => 1,
                    ]);
                    $availableToPay -= $invoiceAmount;
                } elseif ($availableToPay > 0) {
                    // دفع جزئي
                    $inv->update([
                        'paid_amount' => $availableToPay,
                        'invoice_staute' => 2,
                    ]);
                    // إنشاء دين للفاتورة المتبقية
                    $inv->debts()->create([
                        'description' => 'رصيد افتتاحي للمورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => $availableToPay,
                        'remaining' => $invoiceAmount - $availableToPay,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                    $availableToPay = 0;
                    break;
                } else {
                    // لم يتم دفع شيء للفاتورة
                    $inv->update([
                        'paid_amount' => 0,
                        'invoice_staute' => 0,
                    ]);
                    $inv->debts()->create([
                        'description' => 'رصيد افتتاحي للمورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => 0,
                        'remaining' => $invoiceAmount,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                }
            }

            // 7. تحديث رصيد المورد = مجموع كل الفواتير - مجموع المدفوعات
            $totalInvoicesAmount = Supplier_invoice::where('supplier_id', $supplier->id)
                ->sum('total_amount_invoice');

            $supplierBalance = $totalInvoicesAmount - $totalPaid;

            $supplier->account()->update([
                'current_balance' => $supplierBalance,
            ]);

            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم عمل رصيد افتتاحي للمورد وتحديث حالات الدفع والرصيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
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

            // جمع المدفوعات بالقيمة المطلقة
            $totalPaid = $supplier->paymentTransactions()
                ->get()
                ->sum(fn($payment) => abs($payment->amount));

            // مجموع الفواتير المدفوعة بالكامل
            $fullyPaidTotal = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', 1)
                ->sum('total_amount_invoice');

            $availableToPay = $totalPaid - $fullyPaidTotal;

            // جلب كل الفواتير غير المدفوعة أو مدفوعة جزئياً
            $unpaidInvoices = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', '!=', 1)
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->get();

            // حذف ديون سابقة احتياطياً
            foreach ($unpaidInvoices as $inv) {
                $inv->debts()->delete();
            }

            // توزيع المبلغ المتاح على الفواتير الغير مدفوعة
            foreach ($unpaidInvoices as $inv) {
                $invoiceAmount = $this->normalizeNumber($inv->total_amount_invoice);

                if ($availableToPay >= $invoiceAmount) {
                    $inv->update([
                        'paid_amount' => $invoiceAmount,
                        'invoice_staute' => 1,
                    ]);
                    $availableToPay -= $invoiceAmount;
                } elseif ($availableToPay > 0) {
                    $inv->update([
                        'paid_amount' => $availableToPay,
                        'invoice_staute' => 2,
                    ]);
                    $inv->debts()->create([
                        'description' => 'دين مورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => $availableToPay,
                        'remaining' => $invoiceAmount - $availableToPay,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                    $availableToPay = 0;
                    break;
                } else {
                    $inv->update([
                        'paid_amount' => 0,
                        'invoice_staute' => 0,
                    ]);
                    $inv->debts()->create([
                        'description' => 'دين مورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => 0,
                        'remaining' => $invoiceAmount,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                }
            }

            // تحديث رصيد المورد
            $totalInvoicesAmount = Supplier_invoice::where('supplier_id', $supplier->id)
                ->sum('total_amount_invoice');

            $supplierBalance = $totalInvoicesAmount - $totalPaid;

            $supplier->account()->update([
                'current_balance' => $supplierBalance,
            ]);

            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم تعديل الرصيد الافتتاحي بنجاح مع ضبط الديون والرصيد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

  
    protected function updateCredit($request)
    {
        DB::beginTransaction();
        try {
            $supplier = Supplier::findOrFail($request->supplier_id);

            // 1. تحديث بيانات الفاتورة التي نعدلها أولاً
            $invoiceToUpdate = Supplier_invoice::findOrFail($request->id);

            $cost_total = $this->normalizeNumber($request->additional_cost);
            $newAmount = $this->normalizeNumber($request->total_amount_invoice);
            $total_amount = $this->normalizeNumber($request->total_amount);

            $invoiceToUpdate->update([
                'invoice_date' => $request->invoice_date,
                'total_amount' => $total_amount,
                'total_amount_invoice' => $newAmount,
                'cost_price' => $request->additional_cost,
                'notes' => $request->notes,
            ]);

            // تحديث حركة الخزنة للتكاليف إن وجدت
            if ($invoiceToUpdate->transaction) {
                $invoiceToUpdate->transaction()->update([
                    'amount' => -$cost_total,
                ]);
            }

            // تحديث التكاليف الإضافية إن وُجدت
            $costs = $request->input('costs');
            if ($costs && is_array($costs)) {
                foreach ($costs as $cost) {
                    $exponse = Exponse::where('expense_item_id', $cost['exponse_id'])->first();
                    if ($exponse) {
                        $exponse->update(['amount' => $cost['amount']]);
                    }
                }
            }

            // 2. جمع كل المدفوعات للمورد (كمبلغ إيجابي)
            $totalPaid = $supplier->paymentTransactions()
                ->get()
                ->sum(function ($payment) {
                    return abs($payment->amount);
                });

            // 3. احصل على كل الفواتير المدفوعة بالكامل
            $fullyPaidInvoicesTotal = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', 1) // مدفوعة بالكامل
                ->sum('total_amount_invoice');

            // 4. المبلغ المتاح للتوزيع على الفواتير غير المدفوعة
            $availableToPay = $totalPaid - $fullyPaidInvoicesTotal;

            // 5. جلب كل الفواتير غير المدفوعة أو المدفوعة جزئياً بالترتيب
            $unpaidInvoices = Supplier_invoice::where('supplier_id', $supplier->id)
                ->where('invoice_staute', '!=', 1)
                ->orderBy('invoice_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // 6. حذف ديون قديمة لجميع هذه الفواتير (احترازي)
            foreach ($unpaidInvoices as $inv) {
                $inv->debts()->delete();
            }

            // 7. توزيع المبلغ المتاح على الفواتير غير المدفوعة
            foreach ($unpaidInvoices as $inv) {
                $invoiceAmount = $this->normalizeNumber($inv->total_amount_invoice);

                if ($availableToPay >= $invoiceAmount) {
                    // دفع كامل للفاتورة
                    $inv->update([
                        'paid_amount' => $invoiceAmount,
                        'invoice_staute' => 1,
                    ]);
                    $availableToPay -= $invoiceAmount;
                } elseif ($availableToPay > 0) {
                    // دفع جزئي
                    $inv->update([
                        'paid_amount' => $availableToPay,
                        'invoice_staute' => 2,
                    ]);
                    // إنشاء دين للفاتورة المتبقية
                    $inv->debts()->create([
                        'description' => 'دين جزئي على الفاتورة للمورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => $availableToPay,
                        'remaining' => $invoiceAmount - $availableToPay,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                    $availableToPay = 0;
                    break;
                } else {
                    // لم يتم دفع شيء للفاتورة
                    $inv->update([
                        'paid_amount' => 0,
                        'invoice_staute' => 0,
                    ]);
                    $inv->debts()->create([
                        'description' => 'دين على الفاتورة للمورد ' . $supplier->name,
                        'amount' => $invoiceAmount,
                        'paid' => 0,
                        'remaining' => $invoiceAmount,
                        'is_paid' => 0,
                        'date' => $inv->invoice_date,
                    ]);
                }
            }

            // 8. تحديث رصيد المورد = مجموع كل الفواتير - مجموع المدفوعات
            $totalInvoicesAmount = Supplier_invoice::where('supplier_id', $supplier->id)
                ->sum('total_amount_invoice');

            $supplierBalance = $totalInvoicesAmount - $totalPaid;

            $supplier->account()->update([
                'current_balance' => $supplierBalance,
            ]);

            // 9. تحديث المخزون للفاتورة المعدلة
            $this->updateStock($request, $invoiceToUpdate);

            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم تعديل الفاتورة وتحديث حالات الدفع والرصيد بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    protected function updateCash($request)
    {
        DB::beginTransaction();
        try {
            $invoice = Supplier_invoice::findOrFail($request->id);
    
            $total_amount = $this->normalizeNumber($request->total_amount);
            $newAmount    = $this->normalizeNumber($request->total_amount_invoice);
            $oldAmount    = $invoice->total_amount_invoice;
            $cost_total   = $this->normalizeNumber($request->additional_cost);
    
            // تحديث بيانات الفاتورة
            $invoice->update([
                'invoice_date'         => $request->invoice_date,
                'total_amount'         => $total_amount,
                'total_amount_invoice' => $newAmount,
                'paid_amount'          => $newAmount, // كاش → مدفوع بالكامل
                'cost_price'           => $request->additional_cost,
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
                    }
                }
            }
    
            // تعديل حركة المعاملات (الخزنة) حسب المبلغ الجديد
            Account_transactions::where('source_code', $invoice->invoice_code)->update([
                'amount' => -$newAmount
            ]);
    
            Wallet_movement::where('source_code', $invoice->invoice_code)->update([
                'amount' => -$newAmount
            ]);

            // تحديث رصيد المورد
            $supplier = Supplier::findOrFail($request->supplier_id);
            $totalPaid = $supplier->paymentTransactions()
            ->get()
            ->sum(function ($payment) {
                return abs($payment->amount);
            });

            $totalInvoicesSum = Supplier_invoice::where('supplier_id', $supplier->id)->sum('total_amount_invoice');

            $newBalance = $totalInvoicesSum - $totalPaid;

            $supplier->account()->update([
                'current_balance' => $newBalance,
            ]);
    
            // ضبط المخزون بعد التعديل
            $this->updateStock($request, $invoice);
    
            DB::commit();
            return redirect()->route('supplier.index')->with('success', 'تم تعديل فاتورة المورد الكاش بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
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
            'amount'     => $amount,
            'transaction_type' => 'payment',
            'related_type' => Supplier::class,  
            'related_id' => $supplier->id,
            'description' => $request->description
        ]);
        
        // تسجيل دفعة للمورد
        paymentTransaction::create([
            'related_type' => Supplier::class,
            'related_id' => $supplier->id,
            'source_type' => null, 
            'source_id' => null,   
            'direction' => 'in',
            'amount' => $amount,
            'payment_date' => now()->toDateString(),
            'method' => $request->method,
            'description' => $request->description ?? 'دفعة مقدمة'
        ]);
    
        $totalInvoices = Supplier_invoice::where('supplier_id', $supplier->id)->where('invoice_type', '!=', 'cash')->sum('total_amount_invoice');
        $totalPayments = $supplier->paymentTransactions()->sum('amount'); // جميع الدفعات موجبة
        
        $supplierBalance = $totalInvoices - $totalPayments; // الفواتير ناقص الدفعات
        
        // تحديث حساب المورد
        $supplier->account()->update([
            'current_balance' => $supplierBalance,
        ]);

        // تحديث رصيد الخزنة
        $transaction = Account_transactions::where('account_id', $warehouse->account->id)->sum('amount');
        $warehouse->account->update([
            'current_balance' => $transaction
        ]);
    
        // تحديث رصيد المحفظة
        $all_wallet_movement = Wallet_movement::where('wallet_id', $request->wallet_id)->sum('amount');
        $wallet->update([
            'current_balance' => $all_wallet_movement
        ]);
    
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

    public function filterBySupplier(Request $request)
    {
        $query = Supplier_invoice::where('supplier_id', $request->supplier_id);
    
        if ($request->filled('searchCode')) {
            $query->where('invoice_code',$request->searchCode);
        }
    
        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->filled('invoice_staute')) {
            if($request->invoice_staute === 'unpaid'){
                $query->where('invoice_staute', 0)->orWhere('invoice_staute', 2);
            }
            else {
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

    public function deleteInv(Request $request){
        $invoice = Supplier_invoice::where('id' ,$request->id)->first();
        $supplier = Supplier::where('id' ,$request->supplier_id)->first();

        if($invoice->debts){
            $invoice->debts()->delete();
        }

        $invoice->items()->delete();

        $stock_movement = Stock_movement::where('source_code', $invoice->invoice_code)->first();
        $stock_id = $stock_movement->stock_id;
        
        $stock = Stock::where('id', $stock_id)->first();
        $stock->initial_quantity -= $stock_movement->amount;
        $stock->remaining -= $stock_movement->amount;
        $stock->save();

        if($stock->initial_quantity == 0 && $stock->remaining == 0){
            $stock->delete();
        }

        $stock_movement->delete();
        $transaction = Account_transactions::where('source_code', $invoice->invoice_code)->exists();
        if($transaction == 1){
            Account_transactions::where('source_code', $invoice->invoice_code)->delete();
        }

        $supplier->account()->decrement('current_balance', $invoice->total_amount_invoice);

        $invoice->delete();

        return redirect()->route('supplier.account.show', $supplier->id)->with('success', 'تم عمل مرتجع بنجاح');

    }
    

}
