@extends('layouts.app')

@section('title', 'Daily Summary')

@section('content')

@if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
<div class="container-fluid px-5 py-4"> 
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3 p-4">
                <div class="card-header text-center rounded-3">
                    <h2 class="mb-0">Daily Summary Report</h2>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form action="{{ route('divanj.sales.summary') }}" method="GET" class="row g-3 align-items-center mb-4">
                        <div class="col-auto">
                            <label for="start_date" class="form-label fw-bold">Start Date:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-auto">
                            <label for="end_date" class="form-label fw-bold">End Date:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                    </form>
                </div>

                @if($startDate && $endDate)
                    <div class="card-body">
                        <!-- Selected Date Range Display -->
                        <h4 class="text-center my-3 text-primary">
                            {{ \Carbon\Carbon::parse($startDate)->format('j F Y') }} - 
                            {{ \Carbon\Carbon::parse($endDate)->format('j F Y') }}
                        </h4>

                        <div class="row">
                        <div class="row">
                        @foreach($attendanceByDate as $date => $records)
                            <!-- Attendance Column (Left) -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($date)->format('l, j F Y') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="border p-3 mb-3 bg-light rounded shadow-sm">
                                            <!-- <h6 class="fw-bold">Attendance</h6> -->
                                            @forelse($records as $record)
                                                <p class="mb-1">
                                                    <strong>{{ $record['employee']->stage_name }}</strong> -
                                                    @if($record['check_in'] && $record['check_out'])
                                                        {{ $record['check_in'] }} to {{ $record['check_out'] }} = 
                                                        {{ $record['hours'] }} hrs
                                                    @else
                                                        <span class="text-warning">Incomplete</span>
                                                    @endif
                                                    @if($record['status_id'] == $absentStatusId)
                                                        - <span class="text-danger">Absent</span>
                                                    @endif
                                                </p>
                                            @empty
                                                <p class="text-muted">No attendance records</p>
                                            @endforelse

                                            <h6 class="mt-2 text-danger fw-bold">Absent</h6>
                                            @forelse($absentEmployeesByDate[$date] ?? [] as $emp)
                                                <p class="mb-1 text-danger"><strong>{{ $emp->stage_name }}</strong> - Absent</p>
                                            @empty
                                                <p class="text-success">No absent employees.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Column (Right) with Totals -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0 fw-bold">Sales - {{ \Carbon\Carbon::parse($date)->format('l, j F Y') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Sales Records -->
                                        <div class="border p-3 mb-3 bg-white rounded shadow-sm">
                                            <h6 class="fw-bold">Sales Records</h6>
                                            @php
                                                $dayTotalQty = 0;
                                                $dayTotalAmount = 0;
                                            @endphp
                                            
                                            @forelse($salesByDate[$date] ?? [] as $sale)
                                                @php
                                                    $dayTotalQty += $sale['cases'];
                                                    $dayTotalAmount += $sale['amount'];
                                                @endphp
                                                <p class="mb-1">
                                                    <strong>{{ $sale['employee']->stage_name }}</strong> - 
                                                    {{ $sale['cases'] }} case(s) - 
                                                    ${{ number_format($sale['amount'], 2) }}
                                                </p>
                                            @empty
                                                <p class="text-muted">No sales recorded</p>
                                            @endforelse
                                        </div>

                                        <!-- Day Totals -->
                                        <div class="border p-3 bg-info text-white rounded shadow-sm">
                                            <h6 class="fw-bold">Day Totals</h6>
                                            <div class="d-flex justify-content-between">
                                                <span>Total Quantity:</span>
                                                <strong>{{ $dayTotalQty }} cases</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <span>Total Amount:</span>
                                                <strong>${{ number_format($dayTotalAmount, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                        <!-- Totals -->
                        <div class="border-top mt-4 pt-3">
                            <h5 class="fw-bold text-success">Total Summary</h5>
                            <p>Total Cases: <strong>{{ $totalCases }}</strong></p>
                            <p>Total Sales: <strong>${{ number_format($totalSales, 2) }}</strong></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endsection