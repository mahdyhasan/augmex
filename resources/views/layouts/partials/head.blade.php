<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Augmex Accounting & HRM">
    <meta name="author" content="Augmex">
    <meta name="robots" content="noindex, nofollow">

    <!-- Title -->
    <title>@yield('title') | {{ config('app.name', 'Augmex') }}</title>

    <!-- Themescript JS -->
    <script src="{{ asset('public/assets/js/theme-script.js') }}" type="b82e39c220b185c3080ff033-text/javascript"></script>

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/assets/img/apple-touch-icon.png') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('public/assets/img/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/assets/img/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/tabler-icons/tabler-icons.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Color Picker CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/%40simonwep/pickr/themes/nano.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.min.css') }}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/select2/css/select2.min.css') }}">

    <!-- Bootstrap Tagsinput CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Summernote CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/plugins/summernote/summernote-lite.min.css') }}">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/feather.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">

    @yield('css')
</head>