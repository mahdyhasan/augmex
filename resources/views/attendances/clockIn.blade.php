@extends('layouts.app')

@section('title', 'Clock In & Clock Out')

@section('content')
<div class="content-wrapper py-5">
    <div class="container d-flex justify-content-center">
        <div class="card shadow-lg rounded-4 p-4" style="width: 100%; max-width: 500px;">
            <div class="card-header text-center bg-white border-0">
                <h2 class="fw-bold">Clock In & Clock Out</h2>
            </div>

            <div class="card-body text-center">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="d-grid gap-3 mt-4">
                    <!-- Log In Button -->
                    <form action="{{ route('attendance.login') }}" method="POST" id="loginForm">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg fw-bold">
                            <i class="ti ti-login"></i> Log In
                        </button>
                    </form>

                    <!-- Update Sales Button -->
                    <a href="javascript:void(0);" class="btn btn-dark btn-lg fw-bold" data-bs-toggle="offcanvas" data-bs-target="#importSalesReport">
                        <i class="ti ti-upload"></i> Update Sales
                    </a>

                    <!-- Log Out Button -->
                    <button id="logoutBtn" class="btn btn-danger btn-lg fw-bold">
                        <i class="ti ti-logout"></i> Log Out
                    </button>

                    <!-- Hidden Logout Form -->
                    <form id="logoutForm" action="{{ route('attendance.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Offcanvas for the Import Form -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="importSalesReport" aria-labelledby="importSalesReportLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="importSalesReportLabel">Import Sales Report</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('divanj.sales.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Upload Excel File</label>
                <input type="file" class="form-control" name="file" id="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</div>




@endsection

@section('js')
<script>
    // Log Out Confirmation
    document.getElementById('logoutBtn').addEventListener('click', function (e) {
        e.preventDefault();

        if (confirm("Have you submitted your sales report for today?")) {
            document.getElementById('logoutForm').submit();
        }
    });

    // Optional: You may remove this Ajax handler if you're not using it anymore
    document.getElementById('loginForm')?.addEventListener('submit', function (e) {
        // If you want to keep default form behavior, just remove this block entirely.
    });
</script>
@endsection
