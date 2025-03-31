@extends('layouts.app')

@section ('title', 'Income Statement')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Income Statement</h4>
                    <div>
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" action="{{ route('accounts.incomeStatement') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Income Statement Summary -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2" class="text-center">Income Statement ({{ $startDate }} to {{ $endDate }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Revenue Section -->

                                <tr class="table-success">
                                    <td><strong>Revenue (Original Currency)</strong></td>
                                    <td class="text-right">{{ number_format($revenueOriginal, 2) }}</td>
                                </tr>
                                <tr class="table-info">
                                    <td><strong>Exchange Rates Applied</strong></td>
                                    <td class="text-right">
                                        @foreach($exchangeRates as $currency => $rate)
                                            {{ $currency }}: {{ $rate }}@if(!$loop->last), @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Revenue (BDT)</strong></td>
                                    <td class="text-right">{{ number_format($revenueBdt, 2) }}</td>
                                </tr>
                                <!-- Expenses Section -->
                                <tr>
                                    <td colspan="2"><strong>Expenses</strong></td>
                                </tr>
                                
                                @foreach($expensesByCategory as $expense)
                                    @if($expense['amount'] > 0)
                                    <tr>
                                        <td class="pl-4">{{ $expense['name'] }}</td>
                                        <td class="text-right">({{ number_format($expense['amount'], 2) }})</td>
                                    </tr>
                                    @endif
                                @endforeach
                                
                                <!-- Total Expenses -->
                                <tr class="table-danger">
                                    <td><strong>Total Expenses</strong></td>
                                    <td class="text-right">({{ number_format($totalExpenses, 2) }})</td>
                                </tr>
                                
                                <!-- Net Income -->
                                <tr class="{{ $netIncome >= 0 ? 'table-primary' : 'table-warning' }}">
                                    <td><strong>Net Income</strong></td>
                                    <td class="text-right">{{ number_format($netIncome, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Charts Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Revenue vs Expenses</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueExpensesChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Expenses Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="expensesChart" height="200px" width="200px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue vs Expenses Chart
    const revExpCtx = document.getElementById('revenueExpensesChart').getContext('2d');
    new Chart(revExpCtx, {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Expenses'],
            datasets: [{
                label: 'Amount',
                data: [{{ $revenueBdt }}, {{ $totalExpenses }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Expenses Breakdown Chart
    const expCtx = document.getElementById('expensesChart').getContext('2d');
    new Chart(expCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($expensesByCategory->pluck('name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($expensesByCategory->pluck('amount')->toArray()) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@endsection