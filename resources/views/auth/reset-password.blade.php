@extends('layouts.auth')

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <a href="{{ url('/') }}" class="brand-logo">
            <x-logo-component />
        </a>

        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h3>تعيين كلمة سر جديدة</h3>

        <form class="auth-login-form mt-2" action="{{ route('password.store') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ $request->email }}">
            <div class="mb-1">
                <label class="form-label">كلمة مرور جديدة</label>
                <input type="password" class="form-control" name="password" />
            </div>
            <div class="mb-1">
                <label  class="form-label">تأكيد كلمة المرور</label>
                <input type="password" class="form-control" name="password_confirmation" />
            </div>
            <button class="btn btn-primary w-100" tabindex="4">إرسال</button>
        </form>

    </div>
</div>
@endsection

