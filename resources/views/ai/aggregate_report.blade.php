@extends('layouts.app')

@section('title', 'Performance Narrative Report')

@section('content')

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-users me-2 text-primary"></i>Team Performance Summary
            <small class="text-muted float-end">{{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
        </h4>
    </div>
    <div class="card-body">
        <!-- Performance Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Total Cases</h6>
                        <h2 class="fw-bold text-primary">{{ number_format($totalSalesQty) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Total Revenue</h6>
                        <h2 class="fw-bold text-success">${{ number_format($totalSalesAmount, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Best Day</h6>
                        <h2 class="fw-bold text-info">{{ Carbon\Carbon::parse($bestDay)->format('M d') }}</h2>
                        <small class="text-muted">{{ $dailySales[$bestDay] }} cases</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase small fw-bold text-muted">Worst Day</h6>
                        <h2 class="fw-bold text-warning">{{ Carbon\Carbon::parse($worstDay)->format('M d') }}</h2>
                        <small class="text-muted">{{ $dailySales[$worstDay] }} cases</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Performers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>Employee</th>
                                <th class="text-end">Cases Sold</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Avg. Cases/Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employeePerformance as $performance)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-title bg-primary rounded-circle">
                                                {{ substr($performance['employee']->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $performance['employee']->user->name }}</h6>
                                            <small class="text-muted">{{ $performance['employee']->designation }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($performance['total_qty']) }}</td>
                                <td class="text-end">${{ number_format($performance['total_amount'], 2) }}</td>
                                <td class="text-end">{{ number_format($performance['total_qty']/$sales->groupBy('employee_id')->count(), 1) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="fas fa-brain me-2 text-purple"></i>AI Sales Forecast</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-{{ $aiInsights['trend'] == 'upward' ? 'success' : ($aiInsights['trend'] == 'downward' ? 'danger' : 'info') }}">
                            <strong>Trend:</strong> {{ ucfirst($aiInsights['trend']) }} 
                            <span class="float-end">{{ $aiInsights['confidence'] }}% confidence</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase small fw-bold text-muted">Next Week Prediction</h6>
                                        <h3 class="fw-bold">{{ number_format($aiInsights['next_week_prediction']) }}</h3>
                                        <small class="text-muted">cases expected</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase small fw-bold text-muted">Peak Days</h6>
                                        <h3 class="fw-bold">{{ implode(' & ', $aiInsights['peak_days']) }}</h3>
                                        <small class="text-muted">best performance days</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-0"><i class="fas fa-wine-bottle me-2 text-danger"></i>Top Selling Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="fw-bold">#1 {{ $topCategories['top_category']['wine_type'] }}</h6>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ ($topCategories['top_category']['total_qty']/$totalSalesQty)*100 }}%" 
                                     aria-valuenow="{{ ($topCategories['top_category']['total_qty']/$totalSalesQty)*100 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($topCategories['top_category']['total_qty']) }} cases
                                </div>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-3">Top Products in this Category:</h6>
                        <ul class="list-group list-group-flush">
                            @foreach($topCategories['top_category']['examples'] as $example)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $example }}
                                <span class="badge bg-primary rounded-pill">Top Seller</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection