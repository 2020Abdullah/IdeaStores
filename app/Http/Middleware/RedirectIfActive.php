<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = env('APP_SECRET_KEY');
        if (!$key) {
            return redirect()->route('support')->with('error', 'مفتاح التفعيل غير موجود في الإعدادات');
        }

        $localApp = App::where('secret_key', $key)->first();

        // 👇 نسحب الحالة من الكاش أو نحدّثها مرة كل 5 دقائق
        $isActive = Cache::remember("app_status_{$key}", now()->addMinutes(5), function () use ($key, $localApp) {
            try {
                $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 

                $context = stream_context_create([
                    'http' => ['timeout' => 1] // ⏱️ تحديد timeout قصير
                ]);

                $response = @file_get_contents("{$firebaseUrl}/subscriptions.json", false, $context);
                $subscriptions = $response ? json_decode($response, true) : null;

                if ($subscriptions && is_array($subscriptions)) {
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
                }
            } catch (\Exception $e) {
                return $localApp?->is_active ?? 0;
            }

            return $localApp?->is_active ?? 0;
        });

        // 🚫 لو التطبيق مفعل → ما تخليش المستخدم يدخل support
        if ($isActive === 1) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
