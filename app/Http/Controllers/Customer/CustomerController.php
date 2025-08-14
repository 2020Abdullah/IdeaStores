<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerInvoices;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['customer'] = Customer::findOrFail($id);
        $data['payments'] = $data['customer']->paymentTransactions()->paginate(100);
        $data['invoices_list'] = CustomerInvoices::where('customer_id' , $id)->paginate(100);
        return view('customer.Account', $data);
    }
}
