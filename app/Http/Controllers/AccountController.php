<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Account_transactions;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(){
        $accounts = Account::with('accountable')->whereIn('type', ['warehouse', 'partner', 'place', 'owner', 'machine'])->where('is_main', 0)->get();
        return view('accounts.index', compact('accounts'));
    }

    public function add(){
        return view('accounts.add');
    }

    public function show($id){
        $account = Account::with('wallets')->findOrFail($id);

        // جلب كل الحركات المالية الخاصة بالحساب
        $transactions = Account_transactions::where('account_id', $account->id)
        ->orderBy('created_at', 'ASC')
        ->paginate(100);

        return view('accounts.show', compact('account', 'transactions'));
    }
}
