@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">ملفك الشخصي</h3>
    </div>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
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
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success waves-effect waves-float waves-light mt-2">
                حفظ البيانات
            </button>
        </div>
    </form>

</div>
@endsection

