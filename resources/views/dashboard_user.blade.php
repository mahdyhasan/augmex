@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">

            <!-- Welcome Message -->
            <div class="col-md-12 mb-3">
                <h3>Welcome, {{ Auth::user()->name }}!</h3>
                <p>Your dashboard provides a summary of your attendance, sales, and payroll details.</p>
            </div>

            <!-- Attendance Summary -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Summary</h5>
                        <p><strong>Present:</strong> {{ $totalPresent }}</p>
                        <p><strong>Absent:</strong> {{ $totalAbsent }}</p>
                        <p><strong>Late:</strong> {{ $totalLate }}</p>
                        <a href="{{ route('attendance.index') }}" class="btn btn-primary">View Attendance</a>
                    </div>
                </div>
            </div>

            <!-- Today’s Attendance -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Today's Attendance</h5>
                        @if($todayAttendance)
                            <p><strong>Check-in:</strong> {{ $todayAttendance->check_in ?? 'Not Checked-in' }}</p>
                            <p><strong>Check-out:</strong> {{ $todayAttendance->check_out ?? 'Not Checked-out' }}</p>
                            <p><strong>Status:</strong> <span class="badge {{ $todayAttendance->isLate ? 'badge-danger' : 'badge-success' }}">
                                {{ $todayAttendance->isLate ? 'Late' : 'On Time' }}
                            </span></p>
                        @else
                            <p>No attendance record for today.</p>
                        @endif
                        <a href="{{ route('attendance.clockIn') }}" class="btn btn-primary">Log Attendance</a>
                    </div>
                </div>
            </div>

            <!-- Sales Performance -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Your Sales Performance</h5>
                        <p><strong>Total Qty:</strong> ${{ number_format($totalSalesQty) }}</p>
                        <p><strong>Total Sales:</strong> ${{ number_format($totalSales, 2) }}</p>
                        <a href="" class="btn btn-primary">View Sales</a>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection



@section('js')



@endsection