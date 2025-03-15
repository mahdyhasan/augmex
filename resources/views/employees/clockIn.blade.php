@extends('layouts.app')

@section('title', 'Clock In')


@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Clock In & Clock Out</h3>
                </div>
                <div class="card-body text-center">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Log In Button -->
                    <form action="{{ route('attendance.login') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">Log In</button>
                    </form>

                    <!-- Log Out Button -->
                    <form action="{{ route('attendance.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">Log Out</button>
                    </form>
                </div>
            </div>
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
                    alert('Logged out successfully!');
                    location.reload();
                } else {
                    alert(data.error || 'Failed to log out.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endsection


