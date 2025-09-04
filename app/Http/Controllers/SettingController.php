<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'user_id'   => auth()->user()->id,
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
}
