<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
        <meta name="description" content="فكرة سمارت">
        <meta name="author" content="فكرة سمارت">
        <title>{{ config('app.name', 'فكرة سمارت') }}</title>
        <link rel="apple-touch-icon" href="{{ asset('assets/images/web/logo.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/web/logo.ico') }}">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    
        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors-rtl.min.css') }}">
        <!-- END: Vendor CSS-->
    
        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/components.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/dark-layout.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/bordered-layout.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/semi-dark-layout.css') }}">
    
        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
        <!-- END: Page CSS-->
    
        <!-- BEGIN: Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/pages/authentication.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/auth.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/custom-rtl.css') }}">
        <!-- END: Custom CSS-->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
        @yield('css')
    </head>
    <body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
        <!-- content -->
        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper container-xxl p-0"></div>
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2" style="background-image: url('{{ asset('assets/images/banner/banner1.jpg') }}')">
                    <div class="auth-inner my-2">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('assets/js/core/app.js') }}"></script>
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>

    @include('layouts.message')
</html>
