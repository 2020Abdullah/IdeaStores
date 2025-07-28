@extends('layouts.auth')

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <a href="{{ url('/') }}" class="brand-logo">
            <x-logo-component />
        </a>

        <form class="auth-login-form mt-2" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-1">
                <label for="login-email" class="form-label">البريد الإلكتروني</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" />
            </div>

            @error('email')
                <div class="alert alert-danger">
                    <p>{{ @$message }}</p>
                </div>
            @enderror

            <div class="mb-1">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="login-password">كلمة السر</label>
                    <a href="{{ route('password.request') }}">
                        <small>هل نسيت كلمة السر ؟</small>
                    </a>
                </div>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="login-password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>

            @error('password')
                <div class="alert alert-danger">
                    <p>{{ @$message }}</p>
                </div>
            @enderror

            <div class="mb-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" tabindex="3" />
                    <label class="form-check-label" for="remember-me">تذكرني</label>
                </div>
            </div>
            <button class="btn btn-primary w-100" tabindex="4">تسجيل الدخول</button>
        </form>

        <p class="text-center mt-2">
            <span>هل ليس لديك حساب</span>
            <a href="{{ route('register') }}">
                <span>إنشاء حساب جديد</span>
            </a>
        </p>
        
    </div>
</div>
@endsection