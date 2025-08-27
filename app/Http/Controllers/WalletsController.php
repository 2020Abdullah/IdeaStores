<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wallet\WalletRequest;
use App\Models\Account_transactions;
use App\Models\Wallet;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsController extends Controller
{
    public function index(){
        $wallets = Wallet::all();
        $warehouses = Warehouse::all();
        return view('wallets.index', compact('wallets', 'warehouses'));
    }

    public function store(WalletRequest $request){
        $wallet = new Wallet();
        $wallet->name = $request->name;
        $wallet->details = $request->details;
        $wallet->save();
        return back()->with('success', 'تم إضافة حساب بنكي بنجاح');
    }

    public function update(WalletRequest $request){
        $wallet = Wallet::findOrFail($request->wallet_id);
        $wallet->name = $request->name;
        $wallet->details = $request->details;
        $wallet->save();
        return back()->with('success', 'تم تعديل بيانات الحساب البنكي بنجاح');
    }

    public function sync(){
        $data['warehouse_list'] = Warehouse::all();
        $data['wallets_list'] = Wallet::all();
        return view('wallets.sync', $data);
    }

    public function syncStore(Request $request){
        $request->validate([
            'Warehouse_ids' => 'required|array|min:1',
            'wallets_ids' => 'required|array|min:1',
        ]);
    
        $warehouseIds = $request->input('Warehouse_ids'); // الخزن المختارة
        $walletIds = $request->input('wallets_ids');     // الحسابات البنكية المختارة
        DB::beginTransaction();
        try {
            foreach ($warehouseIds as $wId) {
                $warehouse = Warehouse::findOrFail($wId);
                $warehouse->wallets()->sync($walletIds); // يقوم بحفظ الروابط ويستبدل القديمة
            }
            DB::commit();
            return redirect()->back()->with('success', 'تم ربط الخزن بالحسابات بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage());
        }
    }

    public function addBalance(Request $request){
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        $wallet = Wallet::findOrFail($request->wallet_id);
        $warehouse = Warehouse::findOrFail($request->warehouse_id);

    
        // حركة للخزنة
        $warehouse->account->transactions()->create([
            'direction' => 'in',
            'wallet_id' => $request->wallet_id,
            'amount' => $request->amount,
            'transaction_type' => 'added',
            'description' => 'إضافة رصيد للمحفظة ' . $wallet->name,
            'date' => now(),
        ]);
        return back()->with('success', 'تم إضافة الرصيد للمحفظة وتسجيل الحركة في الخزنة بنجاح!');
    }

    public function transactions($id){
        $wallet = Wallet::findOrFail($id);
        $transactions = $wallet->transactions()->paginate(100);
        return view('wallets.transactions', compact('wallet', 'transactions'));
    }

    public function getWalletBalance(Request $request)
    {
        $wallet = Wallet::findOrFail($request->wallet_id);

        return response()->json([
            'status' => true,
            'balance' => $wallet->balance,
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'wallet_id_from'    => 'required|exists:wallets,id',
            'wallet_id_to'      => 'required|exists:wallets,id',
            'balance'           => 'required|numeric|min:0.01',
            'notes'             => 'nullable|string',
        ]);
    
        DB::transaction(function () use ($request) {
            $amount = $request->balance;
    
            //  تسجيل الحركة للخروج من الحساب المصدر
            Account_transactions::create([
                'wallet_id'        => $request->wallet_id_from,
                'direction'        => 'out',
                'amount'           => -$amount,
                'transaction_type' => 'transfer',
                'description'      => $request->notes,
                'date'             => now(),
            ]);
    
            // تسجيل الحركة للدخول في الحساب الهدف
            Account_transactions::create([
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
        $query = Account_transactions::where('wallet_id', $request->wallet_id);

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(100);

        return view('wallets.trans_table', ['transactions' => $transactions])->render();
    }

}
