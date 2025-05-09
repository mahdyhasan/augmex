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

                    <!-- Daily Cards - Showing ALL days including weekends -->
                    <div class="row">
                        @php
                            $start = \Carbon\Carbon::parse($startDate);
                            $end = \Carbon\Carbon::parse($endDate);
                        @endphp
                        
                        @for($date = $start; $date <= $end; $date->addDay())
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $isWeekend = $date->isWeekend();
                                $cardClass = $isWeekend ? 'border-info' : '';
                                $cardHeaderClass = $isWeekend ? 'bg-info text-white' : 'bg-light';
                                $records = $attendanceByDate[$dateStr] ?? [];
                                $salesData = $salesByDate[$dateStr] ?? [];
                            @endphp
                            
                            <div class="col-lg-6 mb-4">
                                <div class="card shadow-sm {{ $cardClass }}">
                                    <div class="card-header {{ $cardHeaderClass }}">
                                        <h5>{{ $date->format('D, j M Y') }}
                                            @if($isWeekend)
                                            <span class="badge bg-warning float-end">Weekend</span>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Attendance and Sales Records</h6>
                                        
                                        @php
                                            // Get all employees for this day (including absent ones)
                                            $dayRecords = collect($records)->map(function ($record) use ($salesByDate, $dateStr) {
                                                $employeeId = $record['employee']->id;
                                                $salesRecord = $salesByDate[$dateStr][$employeeId] ?? null;
                                                $record['cases'] = $salesRecord['cases'] ?? 0;
                                                return $record;
                                            });
                                            
                                            // Add absent employees if no record exists
                                            foreach ($employees as $employee) {
                                                $exists = $dayRecords->contains('employee.id', $employee->id);
                                                if (!$exists) {
                                                    $dayRecords->push([
                                                        'employee' => $employee,
                                                        'check_in' => null,
                                                        'check_out' => null,
                                                        'hours' => 0,
                                                        'status_id' => $absentStatusId,
                                                        'cases' => $salesByDate[$dateStr][$employee->id]['cases'] ?? 0
                                                    ]);
                                                }
                                            }
                                            
                                            $sortedRecords = $dayRecords->sortByDesc('cases');
                                            $dailyTotalCases = collect($salesData)->sum('cases');
                                            $presentAgents = $dayRecords->filter(function ($record) use ($absentStatusId) {
                                                return !($record['status_id'] == $absentStatusId) && 
                                                    ($record['check_in'] && $record['check_out']);
                                            })->count();
                                            $dailyAverage = $presentAgents > 0 ? $dailyTotalCases / $presentAgents : 0;
                                        @endphp
                                        
                                        @foreach($sortedRecords as $record)
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
                                                
                                                @if(($salesByDate[$dateStr][$record['employee']->id]['cases'] ?? 0) > 0)
                                                    <div>
                                                        Sales: {{ $salesByDate[$dateStr][$record['employee']->id]['cases'] }} cases - ${{ number_format($salesByDate[$dateStr][$record['employee']->id]['amount'] ?? 0, 2) }}
                                                    </div>
                                                @else
                                                    <div>No sales recorded</div>
                                                @endif
                                            </div>
                                        @endforeach
                                        
                                        <div class="mt-3 pt-2 border-top">
                                            <strong>Daily Average:</strong> {{ number_format($dailyAverage, 1) }} 
                                            ({{ $dailyTotalCases }} / {{ $presentAgents }} present agents)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Summary Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5>Total Sales and Attendance Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                // Calculate working days (excluding weekends) and agent days
                                $workingDays = 0;
                                $totalAgentDays = 0;
                                $weekendSales = 0;
                                $weekendDays = 0;
                                $start = \Carbon\Carbon::parse($startDate);
                                $end = \Carbon\Carbon::parse($endDate);
                                
                                while ($start <= $end) {
                                    $dateStr = $start->format('Y-m-d');
                                    $isWeekend = $start->isWeekend();
                                    
                                    if ($isWeekend) {
                                        $weekendDays++;
                                        $weekendSales += collect($salesByDate[$dateStr] ?? [])->sum('cases');
                                    } else {
                                        $workingDays++;
                                        $presentAgents = isset($attendanceByDate[$dateStr]) ? 
                                            collect($attendanceByDate[$dateStr])->filter(function ($record) use ($absentStatusId) {
                                                return !($record['status_id'] == $absentStatusId) && 
                                                       ($record['check_in'] && $record['check_out']);
                                            })->count() : 0;
                                        $totalAgentDays += $presentAgents;
                                    }
                                    $start->addDay();
                                }
                                
                                $totalAllSales = $totalCases + $weekendSales;
                                $rangeAverage = $totalAgentDays > 0 ? $totalAllSales / $totalAgentDays : 0;
                            @endphp
                            
                            <div class="alert alert-primary">
                                <h6>Performance Summary</h6>
                                <div>
                                    <strong>Total Sales:</strong> {{ $totalAllSales }} cases
                                    <ul class="mt-2">
                                        <li>Weekday Sales: {{ $totalCases }} cases ({{ $workingDays }} days)</li>
                                        @if($weekendDays > 0)
                                        <li>Weekend Sales: {{ $weekendSales }} cases ({{ $weekendDays }} days)</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mt-2">
                                    <strong>Weighted Average:</strong> {{ number_format($rangeAverage, 2) }} cases per agent day
                                    ({{ $totalAllSales }} / {{ $totalAgentDays }} agent days)
                                </div>
                            </div>

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
    .border-top {
        border-top: 1px solid #dee2e6!important;
    }
    .border-info {
        border: 1px solid #0dcaf0;
    }
</style>
@endsection