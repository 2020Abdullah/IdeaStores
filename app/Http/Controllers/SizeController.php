<?php

namespace App\Http\Controllers;

use App\Http\Requests\size\SizeRequest;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(){
        $sizes = Size::all();
        return view('sizes.index', compact('sizes'));
    }
    public function store(SizeRequest $request){
        $size = new Size();
        $size->width = $request->width;
        $size->save();
        return back()->with('success', 'تم إضافة المقاس بنجاح');
    }

    public function update(SizeRequest $request){
        $size = Size::findOrFail($request->id);
        $size->width = $request->width;
        $size->save();
        return back()->with('success', 'تم تعديل المقاس بنجاح');
    }

    public function delete(Request $request){
        $size = Size::findOrFail($request->id);
        $size->delete();
        return back()->with('success', 'تم حذف المقاس بنجاح');
    }

    public function getSizes()
    {
        $sizes = Size::all();
    
        return response()->json([
            'status' => true,
            'data' => $sizes
        ]);
    }
}
