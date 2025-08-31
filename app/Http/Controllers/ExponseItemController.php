<?php

namespace App\Http\Controllers;

use App\Http\Requests\exponseItem\ExponseItemRequest;
use App\Models\Account_transactions;
use App\Models\Exponse;
use App\Models\ExponseItem;
use App\Models\Wallet;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ExponseItemController extends Controller
{
    public function index(){
        $expenseItems = ExponseItem::with('exponses')->get();
        return view('exponses.index', compact('expenseItems'));
    }

    public function add(){
        return view('exponses.add');
    }

    public function edit($id){
        $exponseItem = ExponseItem::findOrFail($id);
        return view('exponses.edit', compact('exponseItem'));
    }

    public function store(ExponseItemRequest $request){
        $exponseItem = new ExponseItem();
        $exponseItem->name = $request->name;
        $exponseItem->is_profit = $request->is_profit;
        $exponseItem->save();
        return redirect()->route('expenses.items')->with('success', 'تم إضافة البند بنجاح');
    }

    public function update(ExponseItemRequest $request){
        $exponseItem = ExponseItem::findOrFail($request->id);
        $exponseItem->name = $request->name;
        $exponseItem->is_profit = $request->is_profit;
        $exponseItem->save();
        return redirect()->route('expenses.items')->with('success', 'تم تعديل البند بنجاح');
    }

    public function show($id){
        $expenseItem = ExponseItem::findOrFail($id);
        $exponses = Exponse::where('expense_item_id' ,$id)->paginate(100);
        $warehouses = Warehouse::with('wallets')->get();
        return view('exponses.show', compact('expenseItem', 'exponses', 'warehouses'));
    }

    public function payment(Request $request){
        // 1. إنشاء حركة مصروف 
        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        $wallet = Wallet::findOrFail($request->wallet_id);
        $expenseItem = ExponseItem::findOrFail($request->expenseItemId);
        $expenseItem->exponses()->create([
            'expenseable_type' => Wallet::class,
            'expenseable_id' => $wallet->id,
            'account_id' => $warehouse->account->id,
            'amount' => -$request->amount,
            'note' => $request->notes ?? 'مصروفات',
            'date' => now(),
        ]);

        // 2. إنشاء حركة خزنة 
        Account_transactions::create([
            'account_id'       => $warehouse->account->id,
            'wallet_id'        => $request->wallet_id,
            'direction'        => 'out',
            'amount'           => -$request->amount,
            'transaction_type' => 'expense',
            'description'      => $request->notes ?? 'مصروفات',
            'date'             => now(),
        ]);

        return back()->with('success', 'تم إضافة حركة مصروف بنجاح');
    }
}
