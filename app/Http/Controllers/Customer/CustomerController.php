<?php

namespace App\Http\Controllers\Customer;

use App\Exports\CustomerTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Imports\CustomerImport;
use App\Models\App;
use App\Models\Customer;
use App\Models\CustomerInvoices;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class CustomerController extends Controller
{
    public function index(){
        $customer_list = Customer::all();
        return view('customer.index', compact('customer_list'));
    }

    public function add(){
        return view('customer.add');
    }

    public function edit($id){
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function store(CustomerRequest $request){
        DB::beginTransaction();
        try {
            $customer = new Customer();
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->busniess_name = $request->busniess_name;
            $customer->busniess_type = $request->busniess_type;
            $customer->whatsUp = $request->whatsUp;
            $customer->place = $request->place;
            $customer->notes = $request->notes;
            $customer->save();

            $customer->account()->create([
                'name'     => 'حساب العميل: ' . $customer->name,
                'type' => 'customer',
            ]);
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            return back()->with('error', 'حدث خطأ ما برجاء الإتصال بالدعم للمساعدة');        
        }
        return redirect()->route('customer.index')->with('success', 'تم إضافة بيانات العميل بنجاح');
    }

    public function update(CustomerRequest $request){
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($request->id);
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->busniess_name = $request->busniess_name;
            $customer->busniess_type = $request->busniess_type;
            $customer->whatsUp = $request->whatsUp;
            $customer->place = $request->place;
            $customer->notes = $request->notes;
            $customer->save();

            $customer->account()->update([
                'name'     => 'حساب العميل: ' . $customer->name,
            ]);
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            return back()->with('error', 'حدث خطأ ما برجاء الإتصال بالدعم للمساعدة');        
        }
        return redirect()->route('customer.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function showAccount($id){
        $data['warehouse_list'] = Warehouse::all();
        $data['customer'] = Customer::findOrFail($id);
        $data['payments'] = $data['customer']->paymentTransactions()->paginate(100);
        $data['invoices_list'] = CustomerInvoices::where('customer_id' , $id)->paginate(100);
        return view('customer.Account', $data);
    }

    public function exportAccount(Request $request){
        $app = App::latest()->first();
        $customer = Customer::where('id', $request->customer_id)->first();
        $invoices = CustomerInvoices::where('customer_id', $request->customer_id)->latest()->get();
        
        // جلب أول وآخر تاريخ فاتورة وتحويلها لكائنات Carbon
        $firstInvoice = CustomerInvoices::where('customer_id', $request->customer_id)->first();
        $lastInvoice = CustomerInvoices::where('customer_id', $request->customer_id)->latest()->first();
    
        // تحويل التواريخ إلى تنسيق عربي جميل مثلاً d/m/Y
        $first_inv_date = $firstInvoice ? Carbon::parse($firstInvoice->invoice_date)->format('d-m-Y') : '-';
        $last_inv_date = $lastInvoice ? Carbon::parse($lastInvoice->invoice_date)->format('d-m-Y') : '-';
    
        $html = view('customer.show_account_pdf', compact('customer', 'first_inv_date', 'app', 'last_inv_date', 'invoices'))->render();

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
    public function downloadTemplate(){
        return Excel::download(new CustomerTemplateExport, 'نموذج_العملاء.xlsx');
    }

    public function importData(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new CustomerImport, $request->file('file'));

        return back()->with('success', 'تم استيراد العملاء بنجاح');
    }
}
