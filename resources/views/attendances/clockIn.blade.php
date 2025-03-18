@extends('layouts.app')

@section('title', 'Clock In & Clock Out')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid" style="width:50% !important;">
            <div class="card">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <h3>Clock In & Clock Out</h3>
                </div>
                <div class="card-body text-center">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class='row'>
                    <!-- Log In Button -->
                    <div class='col-6'>
                        <form action="{{ route('attendance.login') }}" method="POST" id="loginForm">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">Log In</button>
                    </form>
                    </div>
                    <div class='col-6'>
                        <!-- Log Out Button -->
                        <button class="btn btn-danger btn-lg" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_log_out">
                            Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Offcanvas: Log Out with Sales -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_log_out">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Log Out With Sales</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('attendance.logout') }}" method="POST" id="logoutForm">
            @csrf

            <div class="mb-3">
                <label for="sales_qty" class="form-label">Sales Quantity</label>
                <input type="number" class="form-control" id="sales_qty" name="sales_qty" required>
            </div>
            <div class="mb-3">
                <label for="sales_amount" class="form-label">Sales Amount</label>
                <input type="number" class="form-control" id="sales_amount" name="sales_amount" step="0.01" required>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Logged in successfully!');
                    location.reload();
                } else {
                    alert(data.error || 'Failed to log in.');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Handle logout form submission
        document.getElementById('logoutForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let salesQty = document.getElementById('sales_qty').value;
            let salesAmount = document.getElementById('sales_amount').value;

            if (!salesQty || !salesAmount) {
                alert('Please enter both Sales Quantity and Sales Amount.');
                return;
            }

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    sales_qty: salesQty,
                    sales_amount: salesAmount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Logged out successfully!');
                    window.location.href = "/login"; // Redirect to login page after logout
                } else {
                    alert(data.error || 'Failed to log out.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endsection
