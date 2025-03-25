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
                    <form action="{{ route('sales.summary.dillon') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label fw-bold">Start Date:</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label fw-bold">End Date:</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="client_id" class="form-label fw-bold">Select Client:</label>
                                <select name="client_id" class="form-select" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $index => $client)
                                    <option value="{{ $client->id }}"
                                        {{ (isset($clientId) && $clientId == $client->id) || (!isset($clientId) && $index === 0) ? 'selected' : '' }}>
                                        {{ $client->company }}
                                    </option>
                                @endforeach
                            </select>

                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col text-center">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if($clientId && $startDate && $endDate)
                    <div class="card-body">
                        <!-- Selected Date Range Display -->
                        <h4 class="text-center my-3 text-primary">
                            {{ \Carbon\Carbon::parse($startDate)->format('j F Y') }} - 
                            {{ \Carbon\Carbon::parse($endDate)->format('j F Y') }}
                        </h4>

                        <!-- Loop by Date -->
                        @foreach($attendanceByDate as $date => $records)
                            <div class="mb-4">
                                <h5 class="text-primary fw-bold">{{ \Carbon\Carbon::parse($date)->format('l, j F Y') }}</h5>

                                <!-- Attendance -->
                                <div class="border p-3 mb-2 bg-light rounded">
                                    <h6 class="fw-bold">Attendance</h6>
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

                                    <!-- Absent -->
                                    <h6 class="mt-2 text-danger fw-bold">Absent</h6>
                                    @forelse($absentEmployeesByDate[$date] ?? [] as $emp)
                                        <p class="mb-1 text-danger"><strong>{{ $emp->stage_name }}</strong> - Absent</p>
                                    @empty
                                        <p class="text-success">No absent employees.</p>
                                    @endforelse
                                </div>

                                <!-- Sales -->
                                <div class="border p-3 bg-white rounded">
                                    <h6 class="fw-bold">Sales</h6>
                                    @forelse($salesByDate[$date] ?? [] as $sale)
                                        <p class="mb-1">
                                            <strong>{{ $sale['employee']->stage_name }}</strong> - 
                                            {{ $sale['cases'] }} case(s) - 
                                            ${{ number_format($sale['amount']) }}
                                        </p>
                                    @empty
                                        <p class="text-muted">No sales</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach

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