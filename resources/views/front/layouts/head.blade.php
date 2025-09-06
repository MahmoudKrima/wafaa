<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    @php
    app()->setLocale('ar');
    @endphp
    <title>{{ app('settings')['app_name_' . assetLang()]  }} | @yield('title')</title>
    <!--Favicon-->
    <link rel="icon" href="{{ displayImage(app('settings')['fav_icon']) }}" type="image/jpg">


    <!-- Bootstrap CSS -->
    <link href="{{ asset('front/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Line Awesome CSS -->
    <link href="{{ asset('front/assets/css/line-awesome.min.css') }}" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="{{ asset('front/assets/css/fontAwesomePro.css') }}" rel="stylesheet">
    <!-- Flaticon CSS -->
    <link href="{{ asset('front/assets/css/flaticon.css') }}" rel="stylesheet">
    <!-- Animate CSS-->
    <link href="{{ asset('front/assets/css/animate.css') }}" rel="stylesheet">
    <!-- Magnific Popup Video -->
    <link href="{{ asset('front/assets/css/magnific-popup.css') }}" rel="stylesheet">
    <!-- Owl Carousel CSS -->
    <link href="{{ asset('front/assets/css/owl.carousel.css') }}" rel="stylesheet">
    <!-- Slick Slider CSS -->
    <link href="{{ asset('front/assets/css/slick.css') }}" rel="stylesheet">
    <!-- Nice Select  -->
    <link href="{{ asset('front/assets/css/nice-select.css') }}" rel="stylesheet">
    <!-- Odometer CSS -->
    <link href="{{ asset('front/assets/css/odometer.min.css') }}" rel="stylesheet">
    <!-- Back to Top -->
    <link href="{{ asset('front/assets/css/backToTop.css') }}" rel="stylesheet">
    <!-- Metis Menu -->
    <link href="{{ asset('front/assets/css/metismenu.css') }}" rel="stylesheet">
    <!-- Style CSS -->
    <link href="{{ asset('front/assets/css/style.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/toastr/build/toastr.min.css') }}">

    <!-- jquery -->
    <script src="{{ asset('front/assets/js/jquery-1.12.4.min.js') }}"></script>

</head>