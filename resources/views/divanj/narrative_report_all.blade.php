@extends('layouts.app')

@section('title', 'Team Narrative Report')

@section('css')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            padding: 10px 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-select, .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            border-radius: 5px;
            padding: 8px 20px;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 400px;
            width: 100%;
        }
        .wine-type-badge {
            background-color: #6f42c1;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Team Narrative Report</h1>

        <!-- Date Range Selection Form -->
        <div class="card mb-4">
            <div class="card-header">Select Date Range</div>
            <div class="card-body">
                <form method="GET" action="{{ route('divanj.narrative.report.all') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Header -->
        <div class="alert alert-info text-center mb-4" role="alert">
            Team Report from {{ $startDate }} to {{ $endDate }}
        </div>

        <!-- Summary Metrics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Sales Summary</div>
                    <div class="card-body">
                        <p><strong>Total Cases Sold:</strong> {{ $totalSalesQty }}</p>
                        <p><strong>Total Amount:</strong> ${{ number_format($totalSalesAmount, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Best and Worst Days</div>
                    <div class="card-body">
                        @if($bestDay)
                            <p><strong>Best Day:</strong> {{ $bestDay->date }} ({{ $bestDay->total_qty }} cases)</p>
                        @else
                            <p>No best day data.</p>
                        @endif
                        @if($worstDay)
                            <p><strong>Worst Day:</strong> {{ $worstDay->date }} ({{ $worstDay->total_qty }} cases)</p>
                        @else
                            <p>No worst day data.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Weekly Performance</div>
                    <div class="card-body">
                        @if($bestWeek)
                            <p><strong>Best Week:</strong> Starting {{ $bestWeekStart }} ({{ $bestWeekQty }} cases)</p>
                        @else
                            <p>No best week data.</p>
                        @endif
                        @if($worstWeek)
                            <p><strong>Worst Week:</strong> Starting {{ $worstWeekStart }} ({{ $worstWeekQty }} cases)</p>
                        @else
                            <p>No worst week data.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Patterns -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Day of Week Performance</div>
                    <div class="card-body">
                        @if($bestDayOfWeek)
                            <p><strong>Best Day:</strong> {{ $bestDayOfWeek }}</p>
                            <p><strong>Worst Day:</strong> {{ $worstDayOfWeek }}</p>
                        @else
                            <p>No day of week data available.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Hourly Performance</div>
                    <div class="card-body">
                        @if($bestHourFormatted)
                            <p><strong>Best Hour:</strong> {{ $bestHourFormatted }}</p>
                            <p><strong>Worst Hour:</strong> {{ $worstHourFormatted }}</p>
                        @else
                            <p>No hourly data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Team Attendance</div>
                    <div class="card-body">
                        <p><strong>Total Late Days:</strong> {{ $lateDays }}</p>
                        <p><strong>Total Absent Days:</strong> {{ $absentDays }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wine Performance -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Wine Performance</div>
                    <div class="card-body">
                        @if($topWineType)
                            <h5>Top Wine Type</h5>
                            <p><strong>{{ $topWineType['wine_type'] }}</strong> - {{ $topWineType['total_qty'] }} cases sold</p>
                            <p>Examples: {{ implode(', ', $topWineType['examples']) }}</p>
                        @endif

                        @if($mostSoldProduct)
                            <h5 class="mt-3">Most Sold Product</h5>
                            <p><strong>{{ $mostSoldProduct->name }}</strong> - {{ $mostSoldProduct->total_qty }} cases sold</p>
                        @endif

                        @if($mostSoldItems && $mostSoldItems->isNotEmpty())
                            <h5 class="mt-3">Wine Type Breakdown</h5>
                            <div>
                                @foreach($mostSoldItems as $item)
                                    <span class="wine-type-badge">
                                        {{ $item['wine_type'] }} ({{ $item['total_qty'] }})
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Daily Sales Trend</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- Custom JavaScript for Chart -->
    @if(isset($sales) && $sales->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('salesChart').getContext('2d');
                const salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($sales->pluck('date')->toArray()) !!},
                        datasets: [{
                            label: 'Cases Sold',
                            data: {!! json_encode($sales->pluck('total_qty')->toArray()) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cases Sold'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Team Daily Sales Performance'
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection