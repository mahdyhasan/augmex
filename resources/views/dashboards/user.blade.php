@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Welcome Message -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h3>Welcome, {{ Auth::user()->name }}!</h3>
                <p>Your dashboard gives you insights on attendance, sales, and performance trends.</p>
            </div>
        </div>

        <!-- Dynamic Narrative Summary -->
        <div class="alert alert-dark">
            <h5>📌 Summary</h5>
            <p>
                You have attended <strong>{{ $currentMonthAttendance['present'] }}</strong> days so far this month, with <strong>{{ $currentMonthAttendance['late'] }}</strong> late arrivals and <strong>{{ $currentMonthAttendance['absent'] }}</strong> absences. 
                Your sales this week are <strong>${{ number_format($currentWeekSales['total_amount'], 2) }}</strong>, 
                which is 
                @if ($currentWeekSales['total_amount'] > $lastWeekSales['total_amount'])
                    <span class="text-success">an improvement</span>
                @elseif ($currentWeekSales['total_amount'] < $lastWeekSales['total_amount'])
                    <span class="text-danger">a decrease</span>
                @else
                    <span class="text-warning">about the same</span>
                @endif
                compared to last week.
            </p>
        </div>

        <!-- Insight Cards -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-light shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Warrior of the Week</h6>
                        <h4 class="text-primary">{{ $topPerformerFormatted['name'] ?? 'N/A' }}</h4>
                        <p class="mb-0">{{ number_format($topPerformerFormatted['quantity'] ?? 0) }} case(s)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Weekly Target Progress</h6>
                        <div class="progress mb-1">
                            <div class="progress-bar {{ $progress >= 100 ? 'bg-success' : 'bg-info' }}" 
                                style="width: {{ min($progress, 100) }}%;">
                                {{ round($progress) }}%
                            </div>
                        </div>
                        <small>Target: {{ $goalCases }} cases</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary (Current Month) -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h5><i class="fas fa-calendar-check"></i> Attendance Summary</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="table-success">{{ $currentMonthAttendance['present'] }}</td>
                            <td class="table-danger">{{ $currentMonthAttendance['absent'] }}</td>
                            <td class="table-warning">{{ $currentMonthAttendance['late'] }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary">View Attendance</a>
            </div>

            <div class="col-md-2">
            </div>
            <!-- Pie Chart -->
            <div class="col-md-4">
                <div style="position: relative; height: 200px;">
                    <canvas id="attendancePieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sales Performance (Current Week) -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h5><i class="fas fa-dollar-sign"></i> Sales Performance (This Week)</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Total Quantity</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $currentWeekSales['total_qty'] }}</td>
                            <td>${{ number_format($currentWeekSales['total_amount'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="#" class="btn btn-outline-primary">View Sales</a>
            </div>
        </div>

        <hr>

        <!-- Last 2 Weeks Sales Comparison -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h5><i class="fa fa-bar-chart"></i> Last 2 Weeks Comparison</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Last Week</th>
                            <th>This Week</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${{ number_format($lastWeekSales['total_amount'], 2) }}</td>
                            <td>${{ number_format($currentWeekSales['total_amount'], 2) }}</td>
                            <td>
                                @if ($currentWeekSales['total_amount'] > $lastWeekSales['total_amount'])
                                    <span class="text-success">▲ Selling More</span>
                                @elseif ($currentWeekSales['total_amount'] < $lastWeekSales['total_amount'])
                                    <span class="text-danger">▼ Selling Less</span>
                                @else
                                    <span class="text-warning">➔ No Change</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
<hr>
        <!-- Sales Trend Graph -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h5>📈 Sales Trend (Current Month)</h5>
                <canvas id="salesTrendChart" height="150"></canvas>
            </div>
        </div>

        <!-- Expandable Detailed Sales -->
        <div class="row mb-3">
            <div class="col-md-12">
                <a class="btn btn-outline-secondary" data-bs-toggle="collapse" href="#detailedSales" role="button">
                    Show Daily Sales Details
                </a>
                <div class="collapse mt-2" id="detailedSales">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sales Quantity</th>
                                <th>Sales Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailySales as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale['date'])->format('M d, Y') }}</td>
                                    <td>{{ $sale['sales_qty'] }}</td>
                                    <td>${{ number_format($sale['sales_amount'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Sales Trend Chart
    const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesTrendLabels),
            datasets: [{
                label: 'Quantity',
                data: @json($salesTrendData),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Attendance Pie Chart
    const attendanceCtx = document.getElementById('attendancePieChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [
                    {{ $currentMonthAttendance['present'] }},
                    {{ $currentMonthAttendance['absent'] }},
                    {{ $currentMonthAttendance['late'] }}
                ],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
        }
    });
</script>
@endsection
