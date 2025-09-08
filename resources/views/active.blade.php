@extends('layouts.auth')

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <a href="{{ url('/') }}" class="brand-logo">
            <x-logo-component />
        </a>

        <form class="auth-login-form mt-2" action="{{ route('activeApp') }}" method="POST">
            @csrf
            <div class="mb-1">
                <label for="login-email" class="form-label">مفتاح التفعيل</label>
                <input type="text" class="form-control @error('key') is-invalid @enderror" name="key" value="{{ old('key') }}" />
            </div>

            @error('key')
                <div class="alert alert-danger">
                    <p>{{ @$message }}</p>
                </div>
            @enderror

            <button class="btn btn-primary w-100" tabindex="4">تفعيل</button>
        </form>
        
    </div>
</div>
@endsection