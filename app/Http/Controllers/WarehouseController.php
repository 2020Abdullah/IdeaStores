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

class WarehouseController extends Controller
{
    public function index(){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['all_balance'] = Account::where('type', 'warehouse')->sum('current_balance');
        $data['all_total_capital_balance'] = Account::where('type', 'warehouse')->sum('total_capital_balance');
        $data['all_total_profit_balance'] = Account::where('type', 'warehouse')->sum('total_profit_balance');
        return view('warehouse.index', $data);
    }

    public function store(WareHouseRequest $request){
        try {
            // create warehouse 
            $warehouse = new Warehouse();
            $warehouse->name = $request->name;
            $warehouse->type = $request->type;
            if($request->is_main){
                $warehouse->is_main = $request->is_main;
            }
            $warehouse->save();

            // create account warehouse
            if($request->is_main == 1){
                $warehouse->account()->create([
                    'name' => 'حساب خزنة رئيسية',
                    'type' => 'warehouse',
                    'total_capital_balance' => 0,
                    'total_profit_balance' => 0,
                ]);
            }
            else {
                $warehouse->account()->create([
                    'name' => 'حساب ' . $request->name,
                    'type' => 'warehouse',
                    'total_capital_balance' => 0,
                    'total_profit_balance' => 0,
                ]);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return back()->with('success', 'تم إضافة البيانات بنجاح');
    }

    public function walletsIndex($id){
        $warehouse = Warehouse::findOrFail($id);
        return view('warehouse.wallets', compact('warehouse'));
    }

    public function walletStore(WalletRequest $request){
        $wallet = new Wallet();
        $wallet->account_id = $request->account_id;
        $wallet->name = $request->name;
        $wallet->method = $request->method;
        $wallet->details = $request->details;
        $wallet->current_balance = $request->current_balance;
        $wallet->save();
        return back()->with('success', 'تم إضافة المحفظة بنجاح');
    }

    public function walletUpdate(WalletRequest $request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $wallet->name = $request->name;
        $wallet->method = $request->method;
        $wallet->details = $request->details;
        $wallet->save();
        return back()->with('success', 'تم تعديل بيانات المحفظة بنجاح');
    }

    public function walletShow($id){
        $wallet = Wallet::findOrFail($id);
        return view('warehouse.show', compact('wallet'));
    }

    public function addBalance(Request $request){
        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        $wallet = Wallet::findOrFail($request->wallet_id);

        // تحديث الحسابات
        $result = $request->balance + $warehouse->account->current_balance;
        $warehouse->account->increment('current_balance', $request->balance);
        $warehouse->account->increment('total_capital_balance', $result);
        $wallet->increment('current_balance', $request->balance);

        // تسجيل حركة مالية للحساب 
        Account_transactions::create([
            'account_id' => $warehouse->account->id,
            'direction' => 'in',
            'method' => $wallet->method,
            'amount' => $request->balance,
            'transaction_type'  => 'added',
            'description'      => 'إضافة رصيد إلي الخزنة يدوى'
        ]);

        // تسجيل حركة مالية 
        $wallet_movement = new Wallet_movement();
        $wallet_movement->wallet_id = $wallet->id;
        $wallet_movement->amount = $request->balance;
        $wallet_movement->direction = 'in';
        $wallet_movement->note = 'إضافة رصيد يدوى';
        $wallet_movement->save();

        return back()->with('success', 'تم إضافة رصيد إلي المحفظة بنجاح');
    }

    public function getWalletByWarhouse(Request $request){
        $warehous = Warehouse::findOrFail($request->warehouse_id);
        $wallets = Wallet::where('account_id', $warehous->account->id)->get();
        return response()->json([
            'status' => true,
            'data' => $wallets
        ]);
    }

    public function showTransactions($id){
        $warehouse = Warehouse::findOrFail($id);
        $transactions = Account_transactions::where('account_id', $warehouse->account->id)->paginate(100);
        return view('warehouse.transactions', compact('transactions', 'warehouse'));
    }
}
