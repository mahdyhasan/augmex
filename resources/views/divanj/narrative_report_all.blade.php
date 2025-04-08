@extends('layouts.app')

@section('title', 'Narrative Report - ' . ($employee->user->name ?? 'Employee'))

@section('styles')
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
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Narrative Report</h1>

        <!-- Employee Selection Form -->
        @if(!isset($employee))
            <div class="card mb-4">
                <div class="card-header">Select Employee and Date Range</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('divanj.narrative_report') }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="employee_id" class="form-label">Employee</label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">Select an Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- Report Header -->
            <div class="alert alert-info text-center mb-4" role="alert">
                Report for <strong>{{ $employee->user->name }}</strong> from {{ $startDate }} to {{ $endDate }}
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
                        <div class="card-header">Weekday Performance</div>
                        <div class="card-body">
                            @if($bestWeekday)
                                <p><strong>Best Weekday:</strong> {{ $bestWeekday }} ({{ $bestWeekdayQty }} cases)</p>
                            @else
                                <p>No weekday data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insights and Recommendations -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">AI Insights</div>
                        <div class="card-body">
                            <p><strong>Sales Trend:</strong> {{ ucfirst($aiInsights['trend']) }}</p>
                            <p><strong>Predicted Next Day:</strong> {{ $aiInsights['predicted_cases'] }} cases (Confidence: {{ $aiInsights['confidence'] }}%)</p>
                            @if($aiInsights['anomaly'])
                                <p><strong>Anomaly Detected:</strong> {{ $aiInsights['anomaly'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Recommendations</div>
                        <div class="card-body">
                            <p>{{ $recommendation }}</p>
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
        @endif
    </div>
@endsection

@section('scripts')
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
                                text: 'Daily Sales Performance'
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection