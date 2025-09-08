<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function setting(){
        $app = App::latest()->first();
        return view('setting.company', compact('app'));
    }

    public function profile(){
        return view('setting.profile');
    }

    public function updateSetting(Request $request){
        $app = App::latest()->first();

        $imagePath = '';

        if($app !== null){
            $imagePath = $app->logo;
        }

        if($request->hasFile('logo')){
            $imagePath = $this->imageService->upload($request->file('logo'), 'uploads/setting');
        }

        App::updateOrCreate([
            'secret_key'   => env('APP_SECRET_KEY'),
        ],[
            'logo'     => $imagePath,
            'company_name'     => $request->company_name,
            'company_info'     => $request->company_info,
            'Tax_number'     => $request->Tax_number,
        ]);

        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }
    public function updateProfile(Request $request){
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->id()),
            ],
        ], [
            'email.unique' => 'هذا البريد الإلكتروني مستخدم من قبل، الرجاء استخدام بريد مختلف.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صالح.',
        ]);

        $user = User::where('id', auth()->user()->id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function activeApp(Request $request){
        $request->validate(
            [
                'key' => 'required|string|min:10|max:255',
            ],
            [
                'key.required' => 'الرجاء إدخال المفتاح.',
                'key.string'   => 'المفتاح يجب أن يكون نصياً.',
                'key.min'      => 'المفتاح يجب أن يحتوي على 10 أحرف على الأقل.',
                'key.max'      => 'المفتاح طويل جداً.',
            ]
        );

        $key = $request->input('key');

        // رابط الفايربيز
        $firebaseUrl = rtrim(env('FIREBASE_DB_URL'), '/'); 
        $response = Http::timeout(3)->get("{$firebaseUrl}/subscriptions.json");
        $subscriptions = $response->json() ?: [];
    
        // البحث عن الكلاينت
        $client = collect($subscriptions)
            ->map(fn($item) => (array)$item)
            ->firstWhere('secret_key', $key);
    
        if ($client) {
            $status = ($client['is_active'] ?? 0) == 1 ? 1 : 0;
    
            // تحديث env
            $this->setEnvValue('APP_SECRET_KEY', $key);
    
            // تحديث أو إنشاء في الموديل المحلي
            $localApp = App::first();
            if ($localApp) {
                $localApp->update([
                    'secret_key' => $key,
                    'is_active'  => $status,
                ]);
            } else {
                App::create([
                    'secret_key' => $key,
                    'is_active'  => $status,
                ]);
            }
    
            return redirect()->route('dashboard')->with('success', 'تم تفعيل التطبيق بنجاح');
        }
    
        return back()->with('error', 'مفتاح التفعيل غير صحيح');
    }

    protected function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // لو المفتاح موجود بالفعل
            if (strpos(file_get_contents($path), $key) !== false) {
                file_put_contents(
                    $path,
                    preg_replace(
                        "/^{$key}=.*/m",
                        "{$key}={$value}",
                        file_get_contents($path)
                    )
                );
            } else {
                // لو مش موجود أضف في آخر الملف
                file_put_contents($path, PHP_EOL."{$key}={$value}", FILE_APPEND);
            }
        }

        // تحديث القيم في runtime
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        config([$key => $value]);

        // reload config
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }

}
