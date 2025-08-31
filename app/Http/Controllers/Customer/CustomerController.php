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
        $customer_list = Customer::where('user_id', $this->user_id)->get();
        return view('customer.index', compact('customer_list'));
    }

    public function add(){
        return view('customer.add');
    }

    public function edit($id){
        $customer = Customer::where('id', $id)->where('user_id', $this->user_id)->firstOrFail();
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
            $customer->user_id = $this->user_id;
            $customer->save();

            $customer->account()->create([
                'name' => 'حساب العميل: ' . $customer->name,
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
            $customer = Customer::where('id', $request->id)
                                ->where('user_id', $this->user_id)
                                ->firstOrFail();

            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->busniess_name = $request->busniess_name;
            $customer->busniess_type = $request->busniess_type;
            $customer->whatsUp = $request->whatsUp;
            $customer->place = $request->place;
            $customer->notes = $request->notes;
            $customer->save();

            $customer->account()->update([
                'name' => 'حساب العميل: ' . $customer->name,
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
        $data['customer'] = Customer::where('id', $id)
                                    ->where('user_id', $this->user_id)
                                    ->firstOrFail();
        $data['payments'] = $data['customer']->paymentTransactions()->paginate(100);
        $data['invoices_list'] = CustomerInvoices::where('customer_id', $id)
                                                ->where('user_id', $this->user_id)
                                                ->orderBy('date', 'desc')
                                                ->paginate(100);
        return view('customer.Account', $data);
    }

    public function exportAccount(Request $request){
        $app = App::latest()->first();
        $customer = Customer::where('id', $request->customer_id)
                            ->where('user_id', $this->user_id)
                            ->firstOrFail();

        $invoices = CustomerInvoices::where('customer_id', $request->customer_id)
                                    ->where('user_id', $this->user_id)
                                    ->latest()
                                    ->get();

        $firstInvoice = CustomerInvoices::where('customer_id', $request->customer_id)
                                        ->where('user_id', $this->user_id)
                                        ->first();
        $lastInvoice = CustomerInvoices::where('customer_id', $request->customer_id)
                                       ->where('user_id', $this->user_id)
                                       ->latest()
                                       ->first();

        $first_inv_date = $firstInvoice ? Carbon::parse($firstInvoice->invoice_date)->format('d-m-Y') : '-';
        $last_inv_date = $lastInvoice ? Carbon::parse($lastInvoice->invoice_date)->format('d-m-Y') : '-';

        $html = view('customer.show_account_pdf', compact('customer', 'first_inv_date', 'app', 'last_inv_date', 'invoices'))->render();

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
