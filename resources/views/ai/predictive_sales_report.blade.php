@extends('layouts.app')

@section('title', 'Predictive Sales Report')

@section('css')
<style>
    .report-container {
        background-color: #f8f9fa;
        padding: 2rem 0;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
    }
    .card-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0;
    }
    .metric-card {
        border-left: 4px solid #3b7ddd;
        padding: 1rem;
        height: 100%;
    }
    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
    }
    .metric-label {
        font-size: 0.875rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .trend-up {
        color: #27ae60;
    }
    .trend-down {
        color: #e74c3c;
    }
    .trend-neutral {
        color: #7f8c8d;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .prediction-card {
        background-color: #f8f9fa;
        border-left: 4px solid #3498db;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }
    .btn-primary {
        background-color: #3b7ddd;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
    }
    .badge-indicator {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 50px;
    }
</style>
@endsection

@section('content')
<div class="report-container">
    <div class="container-fluid">
        <!-- Selection Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Select Employee and Date Range</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('divanj.predictive.report') }}" id="reportForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control" required>
                                <option value="">Choose an employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp['id'] }}" @selected(($employee['id'] ?? null) == $emp['id'])>
                                        {{ $emp['user']['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   value="{{ $startDate }}" required max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   value="{{ $endDate }}" required max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-chart-line me-1"></i> Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @isset($employee)
            <!-- Report Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">Sales Report for {{ $employee['user']['name'] }}</h3>
                            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="metric-label">Total Cases Sold</div>
                        <div class="metric-value">{{ number_format($performanceSummary['totalCasesSold']) }}</div>
                        <div class="mt-2">
                            <!-- <span class="badge badge-indicator bg-success">+12% vs last period</span> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="metric-label">Total Revenue</div>
                        <div class="metric-value">${{ number_format($performanceSummary['totalRevenue'], 2) }}</div>
                        <div class="mt-2">
                            <!-- <span class="badge badge-indicator bg-success">+8% vs last period</span> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="metric-label">Best Day</div>
                        <div class="metric-value">{{ $performanceSummary['bestDate'] }}</div>
                        <div class="mt-2">
                            <span class="badge badge-indicator bg-success">{{ number_format($performanceSummary['bestDaySales'] ?? 0) }} cases</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="metric-label">Worst Day</div>
                        <div class="metric-value">{{ $performanceSummary['worstDate'] }}</div>
                        <div class="mt-2">
                            <span class="badge badge-indicator bg-danger">{{ number_format($performanceSummary['worstDaySales'] ?? 0) }} cases</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Sales Performance</h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance Details -->
            <div class="row mb-4">
                <!-- Attendance -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Attendance</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="metric-label">Late Days</div>
                                    <div class="metric-value">{{ $attendance['lateDays'] }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-indicator @if($attendance['lateDays'] > 0) bg-warning text-dark @else bg-success @endif">
                                        @if($attendance['lateDays'] > 0) Needs improvement @else On track @endif
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="metric-label">Absent Days</div>
                                    <div class="metric-value">{{ $attendance['absentDays'] }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-indicator @if($attendance['absentDays'] > 0) bg-danger @else bg-success @endif">
                                        @if($attendance['absentDays'] > 0) High @else Low @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Category -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Top Selling Category</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="metric-label">Wine Type</div>
                                <div class="metric-value">{{ $topCategory['wineType'] }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="metric-label">Cases Sold</div>
                                <div class="metric-value">{{ number_format($topCategory['cases']) }}</div>
                            </div>
                            <div>
                                <div class="metric-label">Best 3 Wines</div>
                                <ul class="list-unstyled mb-0">
                                    @foreach($topCategory['bestWines'] as $wine)
                                        <li class="py-1">{{ $wine }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Indicators -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Performance Indicators</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="metric-label">Best Day of Week</div>
                                <div class="metric-value">{{ $salesPerformance['bestDay'] }}</div>
                                <div class="mt-1">
                                    <!-- <span class="badge badge-indicator bg-success">+22% above average</span> -->
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="metric-label">Worst Day of Week</div>
                                <div class="metric-value">{{ $salesPerformance['worstDay'] }}</div>
                                <div class="mt-1">
                                    <!-- <span class="badge badge-indicator bg-danger">-15% below average</span> -->
                                </div>
                            </div>
                            <div>
                                <div class="metric-label">Best Time to Sell</div>
                                <div class="metric-value">{{ $salesPerformance['bestTime'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Predictions Section -->
            <div class="row">
                <!-- AI Insights -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">AI Sales Predictions</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card prediction-card mb-3">
                                        <div class="card-body">
                                            <div class="metric-label">Next Day Prediction</div>
                                            <div class="metric-value">{{ number_format($aiInsights['nextDayPrediction']) }} cases</div>
                                            <div class="mt-2">
                                                @php
                                                    $trend = $aiInsights['nextDayTrend'] ?? 'neutral';
                                                    $change = $aiInsights['nextDayChange'] ?? 0;
                                                @endphp
                                                <span class="badge badge-indicator @if($trend === 'up') bg-success @elseif($trend === 'down') bg-danger @else bg-secondary @endif">
                                                    @if($trend === 'up') <i class="fas fa-arrow-up"></i> @elseif($trend === 'down') <i class="fas fa-arrow-down"></i> @else <i class="fas fa-minus"></i> @endif
                                                    {{ $change }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card prediction-card mb-3">
                                        <div class="card-body">
                                            <div class="metric-label">Next Week Same Day</div>
                                            <div class="metric-value">{{ number_format($aiInsights['nextWeekSameDayPrediction']) }} cases</div>
                                            <div class="mt-2">
                                                @php
                                                    $trend = $aiInsights['nextWeekTrend'] ?? 'neutral';
                                                    $change = $aiInsights['nextWeekChange'] ?? 0;
                                                @endphp
                                                <span class="badge badge-indicator @if($trend === 'up') bg-success @elseif($trend === 'down') bg-danger @else bg-secondary @endif">
                                                    @if($trend === 'up') <i class="fas fa-arrow-up"></i> @elseif($trend === 'down') <i class="fas fa-arrow-down"></i> @else <i class="fas fa-minus"></i> @endif
                                                    {{ $change }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="metric-label">Sales Forecast</div>
                                <p class="mb-2">{{ $aiInsights['salesForecast'] }}</p>
                                <div class="metric-label mt-2">Trend Analysis</div>
                                <p class="mb-0">
                                    Trend: 
                                    <span class="@if(strtolower($aiInsights['trend'] ?? '') === 'up') text-success @elseif(strtolower($aiInsights['trend'] ?? '') === 'down') text-danger @else text-secondary @endif">
                                        {{ $aiInsights['trend'] ?? 'Neutral' }}
                                        @if(strtolower($aiInsights['trend'] ?? '') === 'up')
                                            <i class="fas fa-arrow-up"></i>
                                        @elseif(strtolower($aiInsights['trend'] ?? '') === 'down')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Insights -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Commission Insights</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="metric-label">Current Week</div>
                                <div class="metric-value">${{ number_format($commissionInsights['currentWeek'] ?? 0, 2) }}</div>
                                <div class="mt-2">
                                    @php
                                        $currentTrend = $commissionInsights['currentWeekTrend'] ?? 'neutral';
                                        $currentChange = $commissionInsights['currentWeekChange'] ?? 0;
                                    @endphp
                                    <span class="badge badge-indicator @if($currentTrend === 'up') bg-success @elseif($currentTrend === 'down') bg-danger @else bg-secondary @endif">
                                        @if($currentTrend === 'up') <i class="fas fa-arrow-up"></i> @elseif($currentTrend === 'down') <i class="fas fa-arrow-down"></i> @else <i class="fas fa-minus"></i> @endif
                                        {{ $currentChange }}%
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="metric-label">Next Week Prediction</div>
                                <div class="metric-value">${{ number_format($commissionInsights['nextWeek'] ?? 0, 2) }}</div>
                                <div class="mt-2">
                                    @php
                                        $nextTrend = $commissionInsights['nextWeekTrend'] ?? 'neutral';
                                        $nextChange = $commissionInsights['nextWeekChange'] ?? 0;
                                    @endphp
                                    <span class="badge badge-indicator @if($nextTrend === 'up') bg-success @elseif($nextTrend === 'down') bg-danger @else bg-secondary @endif">
                                        @if($nextTrend === 'up') <i class="fas fa-arrow-up"></i> @elseif($nextTrend === 'down') <i class="fas fa-arrow-down"></i> @else <i class="fas fa-minus"></i> @endif
                                        {{ $nextChange }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endisset
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date validation
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            
            if (startDate > endDate) {
                alert('End date must be after start date');
                e.preventDefault();
            }
        });

        @isset($employee)
            // Initialize sales chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Cases Sold',
                        data: @json($chartData['quantities']),
                        borderColor: '#3b7ddd',
                        backgroundColor: 'rgba(59, 125, 221, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointBackgroundColor: '#3b7ddd',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: '#2c3e50',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    }
                }
            });
        @endisset
    });
</script>
@endsection