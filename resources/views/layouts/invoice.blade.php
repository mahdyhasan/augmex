<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Salary Sheet Invoice')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        @media print {
            .btn {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        @yield('content')
    </div>
</body>
</html>
