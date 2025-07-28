
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

        @error('email')
            <div class="alert alert-danger">
                <p>{{ @$message }}</p>
            </div>
        @enderror

        <form class="auth-login-form mt-2" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-1">
                <label for="login-email" class="form-label">البريد الإلكتروني</label>
                <input type="text" class="form-control" id="login-email" name="email" />
            </div>
            <button class="btn btn-primary w-100" tabindex="4">إرسال</button>
        </form>

    </div>
</div>
@endsection
