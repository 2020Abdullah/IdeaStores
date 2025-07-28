@extends('layouts.auth')

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <a href="{{ url('/') }}" class="brand-logo">
            <x-logo-component />
        </a>

        <form class="auth-login-form mt-2" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-1">
                <label for="login-name" class="form-label">الإسم</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="login-name" name="name" />
            </div>

            @error('name')
                <div class="alert alert-danger">
                    <p>{{ @$message }}</p>
                </div>
            @enderror

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
                </div>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" name="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="login-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>

            @error('password')
                <div class="alert alert-danger">
                    <p>{{ @$message }}</p>
                </div>
            @enderror

            <div class="mb-1">
                <label class="form-label" for="confim-password">تأكيد كلمة السر</label>
                <div class="input-group input-group-merge form-password-toggle">
                    <input type="password" name="password_confirmation" class="form-control form-control-merge" id="confim-password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                </div>
            </div>

            @error('password_confirmation')
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
            <button class="btn btn-primary w-100" tabindex="4">إنشاء حساب جديد</button>
        </form>

        <p class="text-center mt-2">
            <span>هل لديك حساب ؟</span>
            <a href="{{ route('login') }}">
                <span>سجل دخول الآن</span>
            </a>
        </p>

        <div class="divider my-2">
            <div class="divider-text">أو</div>
        </div>

        <div class="auth-footer-btn d-flex justify-content-center">
            <a href="{{ route('google.login') }}" class="gsi-material-button">
                <div class="gsi-material-button-state"></div>
                <div class="gsi-material-button-content-wrapper">
                  <div class="gsi-material-button-icon">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                      <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                      <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                      <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                      <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                      <path fill="none" d="M0 0h48v48H0z"></path>
                    </svg>
                  </div>
                  <span class="gsi-material-button-contents">Sign up with Google</span>
                  <span style="display: none;">Sign up with Google</span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection