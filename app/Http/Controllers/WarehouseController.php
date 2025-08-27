<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wallet\WalletRequest;
use App\Http\Requests\Warehouse\WareHouseRequest;
use App\Models\Account;
use App\Models\Account_transactions;
use App\Models\Wallet;
use App\Models\Wallet_movement;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(){
        $data['warehouse_list'] = Warehouse::all();
        $data['wallets_list'] = Wallet::all();
        return view('warehouse.index', $data);
    }

    public function store(WareHouseRequest $request){
        try {
            // create warehouse 
            $warehouse = new Warehouse();
            $warehouse->name = $request->name;
            $warehouse->type = $request->type;
            $warehouse->save();

            // create account warehouse
            if($request->is_main == 1){
                $warehouse->account()->create([
                    'name' => 'حساب خزنة رئيسية',
                    'type' => 'warehouse',
                ]);
            }
            else {
                $warehouse->account()->create([
                    'name' => 'حساب ' . $request->name,
                    'type' => 'warehouse',
                ]);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return back()->with('success', 'تم إضافة البيانات بنجاح');
    }

    public function walltetsSync(Request $request){
        $warehouse = Warehouse::findOrFail($request->id);
        $warehouse->wallets()->sync($request->wallets_ids);
        return back()->with('success', 'تم ربط الحسابات بالخزن بنجاح!');
    }

    public function getWalletByWarhouse(Request $request){
        $warehouse = Warehouse::with('wallets')->findOrFail($request->warehouse_id);
        return response()->json([
            'status' => true,
            'data' => $warehouse->wallets
        ]);
    }

    public function showTransactions($id){
        $warehouse = Warehouse::findOrFail($id);

        // جمع كل حركات الخزنة 
        $transactions = $warehouse->account->transactions()->paginate(100);

        return view('warehouse.transactions', [
            'warehouse' => $warehouse,
            'transactions' => $transactions,
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'warehouse_id_from' => 'required|exists:warehouses,id',
            'wallet_id_from'    => 'required|exists:wallets,id',
            'warehouse_id_to'   => 'required|exists:warehouses,id',
            'wallet_id_to'      => 'required|exists:wallets,id',
            'balance'           => 'required|numeric|min:0.01',
            'notes'             => 'nullable|string',
        ]);
    
        DB::transaction(function () use ($request) {
            $amount = $request->balance;
    
            // الحصول على الحسابات المرتبطة بالخزنات
            $accountFrom = Warehouse::find($request->warehouse_id_from)->account;
            $accountTo   = Warehouse::find($request->warehouse_id_to)->account;
    
            // تسجيل الحركة للخروج من الحساب المصدر
            Account_transactions::create([
                'account_id'       => $accountFrom->id,
                'wallet_id'        => $request->wallet_id_from,
                'direction'        => 'out',
                'amount'           => -$amount,
                'transaction_type' => 'transfer',
                'description'      => $request->notes,
                'date'             => now(),
            ]);
    
            // تسجيل الحركة للدخول في الحساب الهدف
            Account_transactions::create([
                'account_id'       => $accountTo->id,
                'wallet_id'        => $request->wallet_id_to,
                'direction'        => 'in',
                'amount'           => $amount,
                'transaction_type' => 'transfer',
                'description'      => $request->notes,
                'date'             => now(),
            ]);
        });
    
        return redirect()->back()->with('success', 'تم تحويل الرصيد بنجاح');
    }

    public function filter(Request $request){
        $query = Account_transactions::where('account_id', $request->account_id);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(100);

        return view('warehouse.trans_table', ['transactions' => $transactions])->render();
    }

}
