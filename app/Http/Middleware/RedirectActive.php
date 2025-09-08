<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RedirectActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = env('APP_SECRET_KEY');

        if (!$key) {
            App::truncate();
            Cache::flush();
            return redirect()->route('active')->with('error', 'مفتاح التفعيل غير موجود محلياً، برجاء تفعيل التطبيق أولاً');
        }
    
        $localApp = App::where('secret_key', $key)->first();
    
        // إذا كان التطبيق مفعّل بالفعل محلياً، فقط انتقل للداشبورد
        if($localApp && $localApp->is_active == 1){
            return redirect()->route('dashboard');
        }
    
        $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
        $response = Http::timeout(3)->get("{$firebaseUrl}/subscriptions.json");
        $subscriptions = $response->json() ?: [];
    
        $client = collect($subscriptions)
            ->map(fn($item) => (array)$item)
            ->firstWhere('secret_key', $key);
    
        if($client && $client['is_active'] == 1){
            $localApp->is_active = 1;
            $localApp->save();
            return redirect()->route('dashboard');
        }
    
        return $next($request);
    }
}
