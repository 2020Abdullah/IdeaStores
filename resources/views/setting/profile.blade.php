@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">معلومات الحساب</h3>
    </div>
    <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="mb-2">
                <label for="name" class="form-label">اسمك</label>
                <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" class="form-control">
            </div>
            <div class="mb-2">
                <label for="email" class="form-label">بريدك الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" class="form-control">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-2">
                <label for="password" class="form-label">كلمة السر</label>
                <input type="password" id="password" placeholder="***********" name="password" class="form-control">
            </div>
            <hr />
            <h3>معلومات الشركة</h3>
            <div class="mb-2">
                <label for="logo" class="form-label">لوجو الشركة</label>
                <input type="file" id="logo" name="logo" class="form-control">
            </div>
            <div class="mb-2">
                <label for="company_name" class="form-label">اسم الشركة</label>
                <input type="text" id="company_name" name="company_name" value="{{ $app->company_name ?? ''}}" class="form-control">
            </div>
            <div class="mb-2">
                <label for="company_info" class="form-label">وصف الشركة</label>
                <textarea name="company_info" class="form-control" id="company_info" cols="5" rows="5">{{ $app->company_info ?? ''}}</textarea>
            </div>
            <div class="mb-2">
                <label for="Tax_number" class="form-label">رقم السجل الضريبي</label>
                <input type="text" id="Tax_number" name="Tax_number" value="{{ $app->Tax_number ?? ''}}" class="form-control">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success waves-effect waves-float waves-light mt-2">
                حفظ البيانات
            </button>
        </div>
    </form>

</div>
@endsection

