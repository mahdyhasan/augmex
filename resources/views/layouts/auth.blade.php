<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Augmex Accounting HRM">
    <meta name="author" content="Augmex">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>@yield('title') | {{ config('app.name', 'Augmex') }} </title>

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/img/apple-touch-icon.png') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('public/assets/img/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/assets/img/favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/tabler-icons/tabler-icons.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
</head>
<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="{{ asset('public/assets/js/jquery-3.7.1.min.js') }}" type="8a923f3333cdf4506d0510f4-text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}" type="8a923f3333cdf4506d0510f4-text/javascript"></script>

    <!-- Feather Icon JS -->
    <script src="{{ asset('public/assets/js/feather.min.js') }}" type="8a923f3333cdf4506d0510f4-text/javascript"></script>

    <!-- Slimscroll JS -->
    <script src="{{ asset('public/assets/js/jquery.slimscroll.min.js') }}" type="8a923f3333cdf4506d0510f4-text/javascript"></script>

    <!-- Custom JS -->
    <script src="{{ asset('public/assets/js/script.js') }}" type="8a923f3333cdf4506d0510f4-text/javascript"></script>

    <script src="{{ asset('public/assets/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js') }}" data-cf-settings="8a923f3333cdf4506d0510f4-|49" defer></script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"91f247055b029855","version":"2025.1.0","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}' crossorigin="anonymous"></script>
</body>
</html>
