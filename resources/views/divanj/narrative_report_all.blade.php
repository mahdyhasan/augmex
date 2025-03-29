@extends('layouts.app')

@section('title', 'Narrative Report')

@section('content')

@if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-gradient-primary text-white shadow-sm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3">
                    <div class="mb-2 mb-md-0">
                        <h3 class="fw-bold mb-0">
                            <i class="fas fa-user-tie me-2"></i>Narrative Report for All
                        </h3>
                    </div>                        
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('divanj.narrative.report') }}" class="btn btn-light text-primary shadow-sm" id="narrativeReportAllBtn">
                            <i class="fas fa-user me-2"></i>Back to Individual Report
                        </a>
                    </div>
                </div>
            </div>            
        </div>

        <div class="card-header">
                <form method="GET" class="report-filter">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase fw-bold">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-lg">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-uppercase fw-bold">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-lg">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-chart-line me-2"></i>Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Summary Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-users me-2"></i>Team Performance Summary
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="performance-stats p-4 rounded bg-light mb-4">
                            <h5 class="text-center mb-4">Overall Team Performance</h5>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span>Reporting Period:</span>
                                <strong>{{ $startDate }} to {{ $endDate }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span>Total Sales:</span>
                                <strong>{{ $totalSalesQty }} cases</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span>Revenue Generated:</span>
                                <strong>${{ number_format($totalSalesAmount) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Attendance Summary:</span>
                                <div>
                                    <span class="badge bg-warning text-dark">{{ $lateDays }} late</span>
                                    <span class="badge bg-danger">{{ $absentDays }} absent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        @if($topWineType)
                        <div class="p-4 rounded bg-info bg-opacity-100 border border-info">
                            <h5 class="text-white mb-3">
                                <i class="fas fa-trophy me-2"></i>Top Performing Wine
                            </h5>
                            <h3 class="text-white">{{ $topWineType['wine_type'] }}</h3>
                            <p class="text-white display-6 mb-1">{{ $topWineType['total_qty'] }} bottles sold</p>
                            @if(!empty($topWineType['examples']))
                            <p class="small text-white-70 mt-2 mb-0">
                                <i class="fas fa-wine-bottle me-1"></i> 
                                Examples: {{ implode(', ', $topWineType['examples']) }}
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Performance Highlights -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-star me-2"></i>Date Performance</h5>
                            </div>
                            <div class="card-body">
                                @if($bestDay && $worstDay)
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="text-center">
                                        <div class="text-success">
                                            <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                            <h6>Best Day</h6>
                                            <p class="mb-0">{{ $bestDay->date }}</p>
                                            <h4 class="mt-1">{{ $bestDay->total_qty }} cases</h4>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-danger">
                                            <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                            <h6>Worst Day</h6>
                                            <p class="mb-0">{{ $worstDay->date }}</p>
                                            <h4 class="mt-1">{{ $worstDay->total_qty }} cases</h4>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($bestDayOfWeek && $worstDayOfWeek)
                                <div class="mt-4 pt-3 border-top">
                                    <h6>Weekday Performance Pattern</h6>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-success">{{ $bestDayOfWeek }} (strongest)</span>
                                        <span class="badge bg-danger">{{ $worstDayOfWeek }} (weakest)</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Hourly Insights</h5>
                            </div>
                            <div class="card-body">
                                @if($bestHourFormatted && $worstHourFormatted)
                                <div class="text-center mb-4">
                                    <h6>Peak Time</h6>
                                    <div class="display-4 text-primary mb-3">{{ $bestHourFormatted }}</div>
                                    <p class="small">Highest weekday sales hour</p>
                                </div>
                                <div class="text-center">
                                    <h6>Lowest Activity</h6>
                                    <div class="display-4 text-secondary">{{ $worstHourFormatted }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Sales Chart & Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Sales Trend & Breakdown
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $day)
                                    <tr>
                                        <td>{{ $day->date }}</td>
                                        <td class="text-end">{{ $day->total_qty }}</td>
                                        <td class="text-end">${{ number_format($day->total_amount) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <th>Total</th>
                                        <th class="text-end">{{ $totalSalesQty }}</th>
                                        <th class="text-end">${{ number_format($totalSalesAmount) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endif <!-- Admin Access Only  -->

@endsection

@section('js')
@if(isset($sales) && $sales->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($sales->pluck('date')) !!},
            datasets: [{
                label: 'Sales Quantity',
                data: {!! json_encode($sales->pluck('total_qty')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
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
                    title: { display: true, text: 'Cases' }
                },
                x: {
                    title: { display: true, text: 'Date' }
                }
            }
        }
    });
});
</script>
@endif
@endsection

@section('css')
<style>
    .performance-stats {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid var(--bs-primary);
    }
    .report-filter .form-control, .report-filter .form-select {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .card-header {
        padding: 1.25rem 1.5rem;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .display-6 {
        font-size: 2rem;
        font-weight: 300;
    }
</style>
@endsection