<?php

namespace App\Http\Controllers;

use App\Http\Requests\exponseItem\ExponseItemRequest;
use App\Models\Exponse;
use App\Models\ExponseItem;
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
        $exponseItem->affect_debt = $request->affect_debt;
        $exponseItem->affect_wallet = $request->affect_wallet;
        $exponseItem->save();
        return redirect()->route('expenses.items')->with('success', 'تم إضافة البند بنجاح');
    }

    public function update(ExponseItemRequest $request){
        $exponseItem = ExponseItem::findOrFail($request->id);
        $exponseItem->name = $request->name;
        $exponseItem->affect_debt = $request->affect_debt;
        $exponseItem->affect_wallet = $request->affect_wallet;
        $exponseItem->save();
        return redirect()->route('expenses.items')->with('success', 'تم تعديل البند بنجاح');
    }

    public function show($id){
        $expenseItem = ExponseItem::findOrFail($id);
        $exponses = Exponse::where('expense_item_id' ,$id)->paginate(100);
        return view('exponses.show', compact('expenseItem', 'exponses'));
    }
}
