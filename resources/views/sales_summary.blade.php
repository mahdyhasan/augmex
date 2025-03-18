@extends('layouts.app')

@section('title', 'Daily Summary')

@section('content')

@if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())

<div class="container">
    <h2 class="mb-4">Daily Summary Report</h2>

    <!-- Filter Form -->
    <form action="{{ route('sales.summary') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="date" class="form-label">Select Date:</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" required>
            </div>
            <div class="col-md-4">
                <label for="client_id" class="form-label">Select Client:</label>
                <select name="client_id" class="form-control" required>
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                            {{ $client->company }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </div>
    </form>

    @if($clientId)
        <!-- Display the selected date -->
        <h4 class="mt-3">{{ \Carbon\Carbon::parse($date)->format('j F Y - l') }}</h4>

        <!-- Employee Attendance Section -->
        <h5 class="mt-4"><strong>Employee Attendance</strong></h5>
        <div class="mb-3">
                @foreach($attendance as $record)
            <p>
                <strong>{{ $record->employee->stage_name ?? 'N/A' }}</strong> -
                @if($record->check_in && $record->check_out)
                    {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }} to 
                    {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }} 
                    =
                    @php
                        $start = \Carbon\Carbon::parse($record->check_in);
                        $end = \Carbon\Carbon::parse($record->check_out);
                        echo $start->diffInHours($end) . ' hours';
                    @endphp
                @else
                    <span class="text-danger">No Check-in</span>
                @endif
            </p>
        @endforeach

        </div>

        <!-- Sales Report Section -->
        <h5 class="mt-4"><strong>Sales Report</strong></h5>
        <div>
            @foreach($sales as $sale)
                <p>
                    <strong>{{ $sale->employee->stage_name ?? 'N/A' }}</strong> - 
                    {{ $sale->sales_qty }} case(s) - 
                    ${{ number_format($sale->sales_amount, 2) }}
                </p>
            @endforeach
        </div>
    @endif
</div>

@endif

@endsection
