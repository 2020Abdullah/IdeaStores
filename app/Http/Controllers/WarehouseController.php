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

class WarehouseController extends Controller
{
    public function index(){
        $data['warehouse_list'] = Warehouse::where('is_main', 0)->get();
        $data['wallets_list'] = Wallet::all();
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
}
