@extends('layouts.app')

@section('title', 'Narrative Report')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">

            <div class="card-header">
                <h3>Employee Report</h3>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Generate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @isset($employee)
    <div class="card mb-4">
        <div class="card-header">Narrative Summary</div>
        <div class="card-body">
            <p>
                Between <strong>{{ $startDate }}</strong> and <strong>{{ $endDate }}</strong>,
                <strong>{{ $employee->stage_name }}</strong> achieved <strong>{{ $totalSalesQty }}</strong> sales,
                generating <strong>{{ number_format($totalSalesAmount) }}</strong> in revenue. This reflects their ability
                to consistently engage with customers and close deals. However, their performance varied significantly across
                different days and weeks, highlighting areas of strength and opportunities for improvement.
            </p>

            @if($bestDay && $worstDay)
            <p>
                On a day-to-day basis, their best day was <strong>{{ $bestDay->date }}</strong>,
                achieving <strong>{{ number_format($bestDay->total_amount) }} </strong> in sales. In contrast,
                their lowest performing day was <strong>{{ $worstDay->date }}</strong>,
                with only <strong>{{ number_format($worstDay->total_amount) }} </strong> earned.
                This inconsistency suggests that external factors, such as market demand or personal productivity,
                may have influenced their performance.
            </p>
            @endif

            @if($bestWeek && $worstWeek)
            <p>
                When comparing weekly performance, the week starting <strong>{{ $bestWeek }}</strong> was their strongest,
                generating <strong>{{ number_format($bestWeekAmount) }} </strong>. On the other hand, the week starting
                <strong>{{ $worstWeek }}</strong> was their weakest, with only
                <strong>{{ number_format($worstWeekAmount) }} </strong> earned.
                This dip in performance could be attributed to factors such as reduced market activity or personal challenges.
            </p>
            @endif

            <p>
                In terms of attendance, they were <strong>late on {{ $lateDays }} days</strong> and
                <strong>absent on {{ $absentDays }} days</strong>, indicating a strong work ethic during peak periods.
                However, the correlation between their attendance and sales performance suggests that punctuality and consistent
                presence are critical for maintaining high sales figures. For example, on days when they were late or absent,
                their sales were significantly lower compared to days when they were present and on time.
            </p>
        </div>
    </div>



        <div class="row mb-4">
            <p>
                The sales trend for <strong>{{ $employee->stage_name }}</strong> is visualized below.
            </p>

            <!-- Left Column: Sales Trend Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">Sales Trend</div>
                    <div class="card-body p-3">
                        <div style="position: relative; height: 300px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Right Column: Sales Table -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">Sales Table</div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $day)
                                    <tr>
                                        <td>{{ $day->date }}</td>
                                        <td>{{ $day->total_qty }}</td>
                                        <td>{{ number_format($day->total_amount) }}</td>
                                    </tr>
                                @endforeach
                                <!-- Total Row -->
                                <tr class="table-secondary">
                                    <td><strong>Total</strong></td>
                                    <td><strong>{{ $totalSalesQty }}</strong></td>
                                    <td><strong>{{ number_format($totalSalesAmount) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endisset
    </div>
</div>
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
                label: 'Sales Amount',
                data: {!! json_encode($sales->pluck('total_qty')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Disable aspect ratio to control height manually
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales Amount'
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
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Sales: ${context.raw.toLocaleString()}`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection