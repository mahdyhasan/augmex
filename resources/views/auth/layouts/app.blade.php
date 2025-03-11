<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <!--favicon-->
    {{--    <link rel="icon" href="{{ asset('public/all-assets/favicon/'.@$settingsinfo->favicon)}}" type="image/x-icon">--}}
    {{--<link rel="icon" href="{{ ($settings && $settings->logo) ? asset($settings->logo) : asset('public/assets/img/logo.png') }}" type="image/x-icon">--}}
    <!-- Bootstrap core CSS-->
    <link href="{{ asset('public/all-assets/expert/assets/css/bootstrap.min.css') }}" rel="stylesheet"/>

    <!-- animate CSS-->
    <link href="{{ asset('public/all-assets/expert/assets/css/animate.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Icons CSS-->
    <link href="{{ asset('public/all-assets/expert/assets/css/icons.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Custom Style-->
    <link href="{{ asset('public/all-assets/expert/assets/css/app-style.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/all-assets/expert/assets/css/custom.css') }}" rel="stylesheet"/>

    @yield('css')
</head>

<body>

@yield('content')

<!-- jQuery -->
<script src="{{ asset('public/all-assets/expert/assets/js/jquery.min.js') }}"></script>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('public/all-assets/expert/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('public/all-assets/expert/assets/js/bootstrap.min.js') }}"></script>

<!--Form Validatin Script-->
<script src="{{ asset('public/all-assets/expert/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>

<!--Sweet Alerts -->
<script src="{{ asset('public/all-assets/expert/assets/plugins/alerts-boxes/js/sweetalert.min.js') }}"></script>

@yield('script')
</body>
</html>
