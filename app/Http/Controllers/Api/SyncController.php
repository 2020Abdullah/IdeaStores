<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function checkAppVerify(Request $request)
    {
        $request->validate([
            'secret_key' => 'required|string'
        ]);
    
        $key = trim($request->input('secret_key'));
    
        $app = App::where('secret_key', $key)->first();
    
        if (!$app) {
            return response()->json([
                'status' => 'error',
                'message' => 'رمز التفعيل غير صالح.',
            ], 404);
        }
    
        if ($app->is_active == 0) {
            return response()->json([
                'status' => 'faild',
                'message' => 'التطبيق غير مفعل حالياً',
            ], 405);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'تم التحقق من التطبيق بنجاح',
            'app' => $app->toArray()
        ]);
    }
}
