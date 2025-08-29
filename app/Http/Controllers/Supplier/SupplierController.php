<?php

namespace App\Http\Controllers\Supplier;
use App\Http\Controllers\Controller;
use App\Exports\SuppliersDataExport;
use App\Exports\SuppliersTemplateExport;
use App\Imports\SuppliersImport;
use App\Models\App;
use App\Models\paymentTransaction;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function showAccount($id){
        $data['warehouse_list'] = Warehouse::all();
        $data['supplier'] = Supplier::findOrFail($id);
        $data['payments'] = $data['supplier']->paymentTransactions()->paginate(100);
        $page = request('page', 1);
        $cacheKey = "customer_{$id}_invoices_page_{$page}";
        $data['invoices_list'] = Cache::remember($cacheKey, 60, function () use ($id) {
            return Supplier_invoice::where('supplier_id', $id)
                ->orderBy('invoice_date', 'desc')
                ->paginate(100);
        });
        return view('suppliers.Account', $data);
    }

    public function profile($id){
        $data['supplier'] = Supplier::findOrFail($id);
        return view('suppliers.profile', $data);
    }

    public function exportAccount(Request $request){
        $app = App::latest()->first();
        $supplier = Supplier::where('id', $request->supplier_id)->first();
        $invoices = Supplier_invoice::where('supplier_id', $request->supplier_id)->latest()->get();
        
        // جلب أول وآخر تاريخ فاتورة وتحويلها لكائنات Carbon
        $firstInvoice = Supplier_invoice::where('supplier_id', $request->supplier_id)->first();
        $lastInvoice = Supplier_invoice::where('supplier_id', $request->supplier_id)->latest()->first();
    
        // تحويل التواريخ إلى تنسيق عربي جميل مثلاً d/m/Y
        $first_inv_date = $firstInvoice ? Carbon::parse($firstInvoice->invoice_date)->format('d-m-Y') : '-';
        $last_inv_date = $lastInvoice ? Carbon::parse($lastInvoice->invoice_date)->format('d-m-Y') : '-';
    
        $html = view('suppliers.show_account_pdf', compact('supplier', 'first_inv_date', 'app', 'last_inv_date', 'invoices'))->render();

        // إعداد mPDF بدعم RTL واللغة العربية
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Arial',
            'default_font_size' => 14
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('account.pdf', 'I'), 200)
            ->header('Content-Type', 'application/pdf');
    }

}
