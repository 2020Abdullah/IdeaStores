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
        $key = env('APP_SECRET_KEY');
        if (!$key) {
            return redirect()->route('support')->with('error', 'مفتاح التفعيل غير موجود في الإعدادات');
        }

        $localApp = App::where('secret_key', $key)->first();

        // 👇 اسحب من الكاش بدل ما تسأل Firebase كل مرة
        $isActive = Cache::remember("app_status_{$key}", now()->addMinutes(5), function () use ($key, $localApp) {
            try {
                $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
                $response = Http::timeout(1)->get("{$firebaseUrl}/subscriptions.json");
                $subscriptions = $response->json() ?: [];
                $client = collect($subscriptions)
                    ->map(fn($item) => (array)$item)
                    ->firstWhere('secret_key', $key);

                if ($client) {
                    $status = ($client['is_active'] ?? 0) == 1 ? 1 : 0;
                    if ($localApp && $localApp->is_active != $status) {
                        $localApp->update(['is_active' => $status]);
                    }
                    return $status;
                }
            } catch (\Exception $e) {
                // في حالة الفشل، رجّع القيمة الحالية من قاعدة البيانات
                return $localApp?->is_active ?? 0;
            }
            return $localApp?->is_active ?? 0;
        });

        if ($isActive === 0) {
            return redirect()->route('support')->with('error', 'التطبيق غير مفعل برجاء الإتصال بالدعم للمساعدة');
        }

        return $next($request);
    }
}
