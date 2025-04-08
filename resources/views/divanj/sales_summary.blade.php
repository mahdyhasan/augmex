@extends('layouts.app')

@section('title', 'Daily Summary')

@section('content')

@if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow border-0">
                <div class="card-header">
                    <h2 class="mb-0">Daily Summary Report</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('divanj.sales.summary') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>

                    <div class="alert alert-info mb-4">
                        Showing results from {{ \Carbon\Carbon::parse($startDate)->format('j M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('j M Y') }}
                    </div>

                    <!-- Daily Cards with Attendance and Sales Records in One Column -->
                    <div class="row">
                        @foreach($attendanceByDate as $date => $records)
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between">
                                    <h5>{{ \Carbon\Carbon::parse($date)->format('D, j M Y') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Attendance and Sales Records</h6>
                    
                                    @php
                                        // Prepare sortable array with case count as the sorting key
                                        $sortedRecords = collect($records)->map(function ($record) use ($salesByDate, $date) {
                                            $employeeId = $record['employee']->id;
                                            $salesRecord = $salesByDate[$date][$employeeId] ?? null;
                                            $record['cases'] = $salesRecord['cases'] ?? 0;
                                            return $record;
                                        })->sortByDesc('cases');
                                    @endphp
                    
                                    @foreach($sortedRecords as $record)
                                        @php
                                            $employeeId = $record['employee']->id;
                                            $salesRecord = $salesByDate[$date][$employeeId] ?? null;
                                        @endphp
                    
                                        <div class="mb-3">
                                            <strong>{{ $record['employee']->stage_name }}</strong>
                                            <div>
                                                Attendance: 
                                                @if($record['status_id'] == $absentStatusId)
                                                    <span class="text-danger">Absent</span>
                                                @elseif($record['check_in'] && $record['check_out'])
                                                    {{ $record['check_in'] }} - {{ $record['check_out'] }} ({{ $record['hours'] }} hours)
                                                @else
                                                    <span class="text-warning">Incomplete</span>
                                                @endif
                                            </div>
                                            
                                            @if($salesRecord)
                                                <div>
                                                    Sales: {{ $salesRecord['cases'] }} cases - ${{ number_format($salesRecord['amount'], 2) }}
                                                </div>
                                            @else
                                                <div>No sales recorded</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>


                    <!-- Summary Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5>Total Sales and Attendance Summary</h5>
                        </div>
                        <div class="card-body">
                            <h6>Attendance Records ({{ $startDate }} - {{ $endDate }})</h6>
                            @foreach($employeeSummaries as $employee)
                                <div>
                                    {{ $employee->stage_name }} worked {{ $employee->present_days }} days, {{ $employee->total_hours_worked }} hours
                                </div>

                            @endforeach

                            <hr>

                            <h6>Sales Records</h6>
                            @foreach($employeeSummaries as $employee)
                                <div>
                                    {{ $employee->stage_name }} - {{ $employee->total_cases }} cases - ${{ number_format($employee->total_sales, 2) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection



@section('css')
<style>
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .table th {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }
</style>
@endsection