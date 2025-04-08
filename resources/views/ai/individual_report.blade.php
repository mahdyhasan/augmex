@extends('layouts.app')

@section('title', 'Performance Narrative Report')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-user-tie me-2 text-primary"></i>Performance Report for {{ $employee->user->name }}
                </h4>
                <small class="text-muted">{{ $employee->designation }}</small>
            </div>
            <div class="text-muted">
                {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Performance Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Cases Sold</h6>
                        <h2 class="fw-bold text-primary">{{ number_format($totalSalesQty) }}</h2>
                        <small class="text-muted">{{ round($totalSalesQty/$sales->count(), 1) }} avg/day</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Revenue</h6>
                        <h2 class="fw-bold text-success">${{ number_format($totalSalesAmount, 2) }}</h2>
                        <small class="text-muted">${{ number_format($totalSalesAmount/$totalSalesQty, 2) }} avg/case</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Best Day</h6>
                        <h2 class="fw-bold text-info">{{ Carbon\Carbon::parse($bestDay->date)->format('M d') }}</h2>
                        <small class="text-muted">{{ $bestDay->total_qty }} cases</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Worst Day</h6>
                        <h2 class="fw-bold text-warning">{{ Carbon\Carbon::parse($worstDay->date)->format('M d') }}</h2>
                        <small class="text-muted">{{ $worstDay->total_qty }} cases</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-info"></i>Sales Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="fas fa-calendar-day me-2 text-success"></i>Best Day</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold text-success">{{ $bestWeekday }}</h1>
                        <p class="text-muted">Average {{ $bestWeekdayQty }} cases</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($bestWeekdayQty/($totalSalesQty/7))*100 }}%" 
                                 aria-valuenow="{{ ($bestWeekdayQty/($totalSalesQty/7))*100 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">{{ round(($bestWeekdayQty/($totalSalesQty/7))*100) }}% better than average</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="fas fa-clock me-2 text-primary"></i>Best Time</h5>
                    </div>
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold text-primary">{{ $bestHourFormatted }}</h1>
                        <p class="text-muted">Peak sales period</p>
                        <div class="mt-3">
                            @foreach($hourlyStats->sortByDesc('qty')->take(3) as $hour => $stats)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ Carbon\Carbon::createFromTime($hour)->format('g A') }}</span>
                                <span>{{ $stats['qty'] }} cases</span>
                            </div>
                            <div class="progress mb-3" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: {{ ($stats['qty']/$hourlyStats->max('qty'))*100 }}%" 
                                     aria-valuenow="{{ ($stats['qty']/$hourlyStats->max('qty'))*100 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="fas fa-user-clock me-2 text-warning"></i>Attendance</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="fw-bold text-danger">{{ $lateDays }}</h2>
                                <p class="text-muted">Late Days</p>
                            </div>
                            <div class="col-6">
                                <h2 class="fw-bold text-warning">{{ $absentDays }}</h2>
                                <p class="text-muted">Absent Days</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Attendance Rate</span>
                                <span>{{ round((($sales->count() - $absentDays)/$sales->count())*100) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ (($sales->count() - $absentDays)/$sales->count())*100 }}%" 
                                     aria-valuenow="{{ (($sales->count() - $absentDays)/$sales->count())*100 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Recommendations -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0"><i class="fas fa-robot me-2 text-purple"></i>AI Recommendations</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="fw-bold"><i class="fas fa-lightbulb me-2"></i>Insights</h5>
                    <p class="mb-0">{{ $aiInsights['anomaly'] ?? 'No significant anomalies detected' }}</p>
                </div>
                
                <div class="alert alert-{{ $advancedPrediction['confidence'] > 70 ? 'success' : 'warning' }}">
                    <h5 class="fw-bold"><i class="fas fa-chart-line me-2"></i>Sales Forecast</h5>
                    <p class="mb-0">
                        Next week prediction: <strong>{{ $advancedPrediction['next_week_prediction'] }} cases</strong> 
                        ({{ $advancedPrediction['confidence'] }}% confidence). 
                        Focus on peak days: {{ implode(', ', $advancedPrediction['peak_days']) }}.
                    </p>
                </div>
                
                <div class="alert alert-primary">
                    <h5 class="fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Commission Strategy</h5>
                    <p class="mb-0">
                        Current efficiency: <strong>${{ $commissionAnalysis['commission_efficiency'] }}/case</strong>. 
                        @if($commissionAnalysis['performance_to_target'] < 80)
                        Focus on high-margin products to reach target (currently at {{ $commissionAnalysis['performance_to_target'] }}%).
                        @else
                        Great job! You're at {{ $commissionAnalysis['performance_to_target'] }}% of target.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Sales Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Cases Sold',
                    data: @json($salesChartData['quantities']),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection