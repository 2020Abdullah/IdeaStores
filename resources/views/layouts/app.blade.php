<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
        <title>{{ config('app.name') }}</title>
        <link rel="apple-touch-icon" href="{{ asset('assets/images/web/logo.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/web/icon.png') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors-rtl.min.css') }}">
    
        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/bootstrap.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/bootstrap-extended.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/colors.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/components.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/dark-layout.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/bordered-layout.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/themes/semi-dark-layout.css') }}">
    
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/extensions/toastr.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/select2.min.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/custom-rtl.css') }}">

        <!-- Fonts -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/fonts.css') }}">
        @yield('css')
    </head>
    <body class="vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="">
        <!-- loading excute -->
        <div id="loading-excute" style="display: none;">
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span>جارى التنفيذ ...</span>
            </div>
        </div>
        <!-- Navbar dashboard -->
        <x-navbar-dashboard />

        <!-- sidebar dashboard -->
        <x-sidebar-dashboard />

        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper container-xxl p-0">
                <div class="content-header row">
                    <!-- content title -->
                    @yield('content-title')
                </div>
                <div class="content-body">
                    <!-- content -->
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- footer -->
        <x-footer-dashboard />
    </body>
    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('assets/js/core/app.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <!-- END: Theme JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })

        // add active url to page target
        var Currentpath = $(location).attr("pathname");
        
        $(".navigation .nav-item").each(function(index, item){
            if($(this).find('a').attr('href').indexOf(Currentpath) !== -1){
                $(".navigation .nav-item").removeClass('active');
                $(this).addClass('active');
            }
        })
    </script>
    @include('layouts.message')
    @yield('js')

    <script>
         $('.select2').select2({
            dir: "rtl",
            width: 'resolve'
        });
    </script>
</html>
