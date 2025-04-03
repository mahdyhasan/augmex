@extends('layouts.app')

@section('title', 'Divanj Dashboard')

@section('content')

@if(Auth::user()->isSuperAdmin())

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Divanj Performance Dashboard</h2>
            <p class="text-muted">Overview of sales and commissions</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employeeCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Units Sold</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($salesTotals->total_units) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Sales Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($salesTotals->total_amount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Commission Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($commissionTotals->total_commission, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Weekly Sales Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Weekly Sales Performance</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 400px; width: 100%; min-height: 300px;">
                        <canvas id="weeklySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th class="text-end">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $employee)
                                <tr>
                                    <td>{{ $employee->stage_name }}</td>
                                    <td class="text-end">{{ number_format($employee->total_commission, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Commissions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Commissions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Week Ending</th>
                                    <th>Employee</th>
                                    <th class="text-end">Target</th>
                                    <th class="text-end">Achieved</th>
                                    <th class="text-end">Commission</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCommissions as $commission)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($commission->end_date)->format('M d, Y') }}</td>
                                    <td>{{ $commission->employee->stage_name }}</td>
                                    <td class="text-end">{{ $commission->target }}</td>
                                    <td class="text-end">{{ $commission->achieved_qty }}</td>
                                    <td class="text-end">{{ number_format($commission->commission_amount, 2) }}</td>
                                    <td>
                                        @if($commission->commission_type == 'fixed')
                                            <span class="badge bg-success">Fixed+Bonus</span>
                                        @elseif($commission->commission_type == 'mixed')
                                            <span class="badge bg-primary">Mixed</span>
                                        @else
                                            <span class="badge bg-danger">Weekend Only</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

@section('css')
<!-- Chart.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .card {
        border-radius: 0.35rem;
    }
    .chart-area {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }
</style>
@endsection

@section('js')
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
<script>
    // Weekly Sales Chart
    document.addEventListener('DOMContentLoaded', function() {
        const weeklySalesData = @json($weeklySales->reverse()->values());
        
        const labels = weeklySalesData.map(item => `Week ${item.week}, ${item.year}`);
        const unitsData = weeklySalesData.map(item => item.units);
        const amountData = weeklySalesData.map(item => item.amount / 1000); // Convert to thousands for scale
        
        const ctx = document.getElementById('weeklySalesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Units Sold',
                        data: unitsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Sales Amount (in 1000s)',
                        data: amountData,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Units Sold'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Sales Amount ( in 1000s)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    });
</script>
@endsection