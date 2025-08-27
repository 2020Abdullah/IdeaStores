<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
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

        try {
            $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
            $response = @file_get_contents("{$firebaseUrl}/subscriptions.json"); // بديل أسرع بدون HTTP Facade
            $subscriptions = $response ? json_decode($response, true) : null;

            if ($subscriptions && is_array($subscriptions)) {
                $client = collect($subscriptions)
                    ->map(fn($item) => (array)$item)
                    ->firstWhere('secret_key', $key);

                if ($client && ($client['is_active'] ?? 0) == 1) {
                    if ($localApp && $localApp->is_active != 1) {
                        $localApp->update(['is_active' => 1]);
                    }
                } elseif ($client && ($client['is_active'] ?? 0) == 0) {
                    if ($localApp && $localApp->is_active != 0) {
                        $localApp->update(['is_active' => 0]);
                    }
                }
                // إذا لم يتم العثور على العميل في Firebase، لا نغير الحالة المحلية
            }

            // إذا التطبيق مفعل محليًا، لا تسمح بالوصول لصفحة support
            if ($localApp && $localApp->is_active === 1) {
                return redirect()->route('dashboard');
            }

        } catch (\Exception $e) {
            // عند عدم الاتصال بـ Firebase، نترك التطبيق كما هو
            if ($localApp && $localApp->is_active === 1) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
