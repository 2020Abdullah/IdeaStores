<?php

namespace App\Http\Controllers;

use App\Http\Requests\Units\UnitsRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(){
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function store(UnitsRequest $request){
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->symbol = $request->symbol;
        $unit->save();
        return back()->with('success', 'تم إضافة البيانات بنجاح');
    }

    public function update(UnitsRequest $request){
        $unit = Unit::where('id', $request->id)->first();
        $unit->name = $request->name;
        $unit->symbol = $request->symbol;
        $unit->save();
        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function delete(Request $request){
        $unit = Unit::where('id', $request->id)->first();
        $unit->delete();
        return back()->with('success', 'تم حذف البيانات بنجاح');
    }

    public function getUnits()
    {
        $units = Unit::all();
    
        return response()->json([
            'status' => true,
            'data' => $units
        ]);
    }
}
