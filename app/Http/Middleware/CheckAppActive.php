<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use App\Models\App;
use Illuminate\Support\Facades\Cache;

class CheckAppActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $localApp = App::first();
        $key = env('APP_SECRET_KEY');
    
        if (!$key) {
            return redirect()->route('support')
                ->with('error', 'مفتاح التفعيل غير موجود محلياً، برجاء التواصل مع المبرمج');
        }
    
        $isActive = Cache::remember("app_status_{$key}", now()->addMinutes(1), function () use ($key, $localApp) {
            try {
                $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
                $response = Http::timeout(3)->get("{$firebaseUrl}/subscriptions.json");
                $subscriptions = $response->json() ?: [];
    
                $client = collect($subscriptions)
                    ->map(fn($item) => (array)$item)
                    ->firstWhere('secret_key', $key);
    
                if (!$client) {
                    return $localApp?->is_active ?? 0;
                }
    
                $status = ($client['is_active'] ?? 0) == 1 ? 1 : 0;
    
                if ($localApp && $localApp->is_active != $status) {
                    $localApp->update(['is_active' => $status]);
                }
    
                return $status;
    
            } catch (\Exception $e) {
                return $localApp?->is_active ?? 0;
            }
        });
    
        if ($isActive === 0 && !session('redirected_to_support')) {
            session(['redirected_to_support' => true]);
            return redirect()->route('support')
                ->with('error', 'التطبيق غير مفعل، برجاء التواصل مع المبرمج للتفعيل');
        }
    
        return $next($request);
    }
}
