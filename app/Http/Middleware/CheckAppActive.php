<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use App\Models\App;

class CheckAppActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = env('APP_SECRET_KEY');
        if (!$key) {
            return redirect()->route('support')->with('error', 'مفتاح التفعيل غير موجود في الإعدادات');
        }

        $localApp = App::where('secret_key', $key)->first();

        try {
            $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
            $response = Http::timeout(3)->get("{$firebaseUrl}/subscriptions.json");
            $subscriptions = $response->json() ?: [];
            $client = collect($subscriptions)
                ->map(fn($item) => (array)$item)
                ->firstWhere('secret_key', $key);

            if ($client && ($client['is_active'] ?? 0) == 1) {
                // إذا مفعل في Firebase، حدّث الحالة المحلية
                if ($localApp && $localApp->is_active != 1) {
                    $localApp->update(['is_active' => 1]);
                }
            } elseif ($client && ($client['is_active'] ?? 0) == 0) {
                // إذا موجود في Firebase وغير مفعل
                if ($localApp && $localApp->is_active != 0) {
                    $localApp->update(['is_active' => 0]);
                }
            }
            // إذا لم يتم العثور على client في Firebase، لا تغيّر الحالة المحلية
        } catch (\Exception $e) {
            return $next($request);
        }

        // إذا التطبيق محليًا غير مفعل، امنع الوصول
        if ($localApp && $localApp->is_active === 0) {
            return redirect()->route('support')->with('error', 'التطبيق غير مفعل برجاء الإتصال بالدعم للمساعدة');
        }

        return $next($request);
    }
}
