@extends('layouts.auth')

@section('css')
    <style>
        .support {
            display: flex;
            flex-direction: column;
            align-content: center;
            justify-content: center;
        }
        .support .social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .support .social a {
            padding: 10px;
            background-color: #21b24b;
            color: #fff;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
<div class="card mb-0">
    <div class="card-body">
        <a href="{{ url('/') }}" class="brand-logo">
            <x-logo-component />
        </a>

        <div class="support">
            <h3>تواصل مع الدعم للمساعدة في تفعيل التطبيق</h3>
            <div class="social">
                <a href="https://www.facebook.com/FekraSmartTech" target="_blank">
                    <i data-feather='facebook'></i>
                </a>
                <a href="https://api.whatsapp.com/send/?phone=201095314681" target="_blank">
                    <i data-feather='phone'></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
