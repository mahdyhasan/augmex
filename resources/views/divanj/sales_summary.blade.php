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

                    <!-- Daily Tables in Two Columns -->
                    @php
                        $start = \Carbon\Carbon::parse($startDate);
                        $end = \Carbon\Carbon::parse($endDate);
                        $dates = [];
                        while ($start <= $end) {
                            $dates[] = $start->copy();
                            $start->addDay();
                        }
                        $chunkedDates = array_chunk($dates, ceil(count($dates) / 2));
                    @endphp
                    
                    <div class="row">
                        @foreach($chunkedDates as $columnDates)
                        <div class="col-md-6">
                            @foreach($columnDates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $isWeekend = $date->isWeekend();
                                    $cardClass = $isWeekend ? 'border-info' : '';
                                    $cardHeaderClass = $isWeekend ? 'bg-info text-white' : 'bg-light';
                                    $records = $attendanceByDate[$dateStr] ?? [];
                                    $salesData = $salesByDate[$dateStr] ?? [];
                                    
                                    // Prepare records for this day
                                    $dayRecords = collect($records)->map(function ($record) use ($salesByDate, $dateStr) {
                                        $employeeId = $record['employee']->id;
                                        $salesRecord = $salesByDate[$dateStr][$employeeId] ?? null;
                                        $record['cases'] = $salesRecord['cases'] ?? 0;
                                        $record['amount'] = $salesRecord['amount'] ?? 0;
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
                                                'cases' => $salesByDate[$dateStr][$employee->id]['cases'] ?? 0,
                                                'amount' => $salesByDate[$dateStr][$employee->id]['amount'] ?? 0
                                            ]);
                                        }
                                    }
                                    
                                    $sortedRecords = $dayRecords->sortByDesc('cases');
                                    
                                    // Calculate totals
                                    $totalPresent = $dayRecords->filter(function ($record) use ($absentStatusId) {
                                        return !($record['status_id'] == $absentStatusId) && 
                                            ($record['check_in'] && $record['check_out']);
                                    })->count();
                                    
                                    $totalAbsent = $dayRecords->filter(function ($record) use ($absentStatusId) {
                                        return $record['status_id'] == $absentStatusId;
                                    })->count();
                                    
                                    $totalLeave = $dayRecords->filter(function ($record) {
                                        return $record['check_in'] === 'Leave';
                                    })->count();
                                    
                                    $totalResigned = $dayRecords->filter(function ($record) {
                                        return $record['check_in'] === 'Resigned';
                                    })->count();
                                    
                                    $totalHours = $dayRecords->sum('hours');
                                    $totalCases = $dayRecords->sum('cases');
                                    $totalAmount = $dayRecords->sum('amount');
                                    
                                    $dailyAverageCases = $totalPresent > 0 ? $totalCases / $totalPresent : 0;
                                    $dailyAverageAmount = $totalPresent > 0 ? $totalAmount / $totalPresent : 0;
                                @endphp
                                
                                <div class="card mb-4 shadow-sm {{ $cardClass }}">
                                    <div class="card-header {{ $cardHeaderClass }}">
                                        <h5>{{ $date->format('j M\'y') }}
                                            @if($isWeekend)
                                            <span class="badge bg-warning float-end">Weekend</span>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="20%">Agent</th>
                                                        <th width="10%">IN</th>
                                                        <th width="10%">OUT</th>
                                                        <th width="10%">HOURS</th>
                                                        <th width="10%">CASES</th>
                                                        <th width="15%">$</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sortedRecords as $index => $record)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td class="text-start">
                                                                <strong>{{ $record['employee']->stage_name }}</strong>
                                                                @if($record['check_in'] === 'Resigned')
                                                                    <span class="badge bg-secondary">Resigned</span>
                                                                @elseif($record['check_in'] === 'Leave')
                                                                    <span class="badge bg-info">Leave</span>
                                                                @elseif($record['status_id'] == $absentStatusId)
                                                                    <span class="badge bg-danger">Absent</span>
                                                                @elseif(!$record['check_in'] || !$record['check_out'])
                                                                    <span class="badge bg-warning">Incomplete</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $record['check_in'] ?? '-' }}</td>
                                                            <td>{{ $record['check_out'] ?? '-' }}</td>
                                                            <td>{{ $record['hours'] > 0 ? $record['hours'] : '-' }}</td>
                                                            <td>{{ $record['cases'] > 0 ? $record['cases'] : '-' }}</td>
                                                            <td>
                                                                @if($record['amount'] > 0)
                                                                    ${{ number_format($record['amount'], 0) }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <td colspan="2">Total In</td>
                                                        <td colspan="2">{{ $totalPresent }}</td>
                                                        <td>{{ $totalHours }}</td>
                                                        <td>{{ $totalCases }}</td>
                                                        <td>${{ number_format($totalAmount, 0) }}</td>
                                                    </tr>
                                                    <!-- <tr class="bg-light">
                                                        <td colspan="2">Total Not In</td>
                                                        <td colspan="2">{{ $totalAbsent + $totalLeave }}</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr> -->
                                                    <tr class="bg-light">
                                                        <td colspan="2">Daily Average</td>
                                                        <td colspan="2"></td>
                                                        <td></td>
                                                        <td>{{ number_format($dailyAverageCases, 1) }}</td>
                                                        <td>${{ number_format($dailyAverageAmount, 0) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

                    <!-- Summary Card -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5>Summary Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Attendance Summary</h6>
                                    @foreach($employeeSummaries as $employee)
                                        <div class="mb-2">
                                            <strong>{{ $employee->stage_name }}</strong>: 
                                            {{ $employee->present_days }} days, 
                                            {{ $employee->total_hours_worked }} hours
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <h6>Sales Summary</h6>
                                    @foreach($employeeSummaries as $employee)
                                        <div class="mb-2">
                                            <strong>{{ $employee->stage_name }}</strong>: 
                                            {{ $employee->total_cases }} cases - 
                                            ${{ number_format($employee->total_sales, 2) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
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
                            </div>
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
        vertical-align: middle;
        text-align: center;
    }
    .table td {
        vertical-align: middle;
        text-align: center;
    }
    .table td.text-start {
        text-align: left;
    }
    .table tbody tr td {
        font-size: 18px !important;
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
    .bg-light {
        background-color: #f8f9fa!important;
    }
    tfoot td {
        font-size: 17px !important;
        font-weight: bold;
    }
    tfoot tr:last-child td {
        border-bottom: 2px solid #dee2e6;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding-right: 15px;
        padding-left: 15px;
    }
    @media (max-width: 767.98px) {
        .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endsection