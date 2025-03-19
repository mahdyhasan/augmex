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
                    <form action="{{ route('sales.summary') }}" method="GET" class="mb-4">
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
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
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
                        <!-- Display the selected date range -->
                        <h4 class="text-center my-3 text-primary">
                            {{ \Carbon\Carbon::parse($startDate)->format('j F Y') }} - 
                            {{ \Carbon\Carbon::parse($endDate)->format('j F Y') }}
                        </h4>

                        <div class="row">
                            <!-- Attendance Column -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 shadow-sm">
                                    <h5 class="text-secondary fw-bold">Employee Attendance</h5>
                                    @if($attendance->isNotEmpty())
                                        @foreach($attendance as $record)
                                            @if($record->check_in || $record->check_out)
                                                <p class="mb-1">
                                                    <strong>{{ $record->employee->stage_name ?? 'N/A' }}</strong> - 
                                                    {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }} to 
                                                    {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }} =
                                                    @php
                                                        $start = \Carbon\Carbon::parse($record->check_in);
                                                        $end   = \Carbon\Carbon::parse($record->check_out);
                                                        echo $start->diffInHours($end) . ' hours';
                                                    @endphp
                                                </p>
                                            @else
                                                <p class="text-danger mb-1">
                                                    <strong>{{ $record->employee->stage_name ?? 'N/A' }}</strong> - Incomplete Record
                                                </p>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-muted">No attendance records found.</p>
                                    @endif

                                    <h6 class="mt-3 text-danger fw-bold">Absent Employees</h6>
                                    @if($absentEmployees->isNotEmpty())
                                        @foreach($absentEmployees as $employee)
                                            <p class="text-danger mb-1">
                                                <strong>{{ $employee->stage_name ?? 'N/A' }}</strong> - Absent
                                            </p>
                                        @endforeach
                                    @else
                                        <p class="text-success">No absent employees.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Sales Report Column -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 shadow-sm">
                                    <h5 class="text-secondary fw-bold">Sales Report</h5>
                                    @if($sales->isNotEmpty())
                                        @foreach($sales as $sale)
                                            @if($sale->sales_qty > 0)
                                                <p class="mb-1">
                                                    <strong>{{ $sale->employee->stage_name ?? 'N/A' }}</strong> - 
                                                    {{ $sale->sales_qty }} case(s) - 
                                                    ${{ number_format($sale->sales_amount, 2) }}
                                                </p>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-muted">No sales records found.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endif

@endsection
