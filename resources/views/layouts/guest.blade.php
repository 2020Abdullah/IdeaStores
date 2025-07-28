<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>فكرة سمارت</title>
        <link rel="stylesheet" href="{{ asset('assets/home/bootstrap.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/home/animate.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/home/all.min.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/home/style.css') }}">
        @yield('css')
    </head>
    <body>
        <x-home.header-section />
        <x-home.loading />
        @yield('content')
        <x-home.footer-section />
        <script src="{{ asset('assets/home/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/home/wow.min.js') }}"></script>
        <script src="{{ asset('assets/home/bootstrap.bundle.min.js') }}"></script>
        <script>
            $(function(){
                $(window).on('load', function () {
                    setTimeout(function () {
                        $('#loading').fadeOut(500);
                    }, 3000); 
                });
            })
        </script>
        @include('layouts.message')
        @yield('js')
    </body>
</html>
