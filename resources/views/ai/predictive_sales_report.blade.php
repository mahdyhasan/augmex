@extends('layouts.app')

@section('title', 'Predictive Sales Report')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    .target-met {
        background-color: #ecfdf5;
        color: #059669;
    }
    .target-missed {
        background-color: #fef2f2;
        color: #dc2626;
    }
    .performance-card {
        transition: all 0.3s ease;
    }
    .performance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .weekday-highlight {
        border-left: 4px solid #3b82f6;
    }
    .weekend-highlight {
        border-left: 4px solid #8b5cf6;
    }
    .motivation-card {
        border-left: 4px solid #10b981;
    }
</style>
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Sales Performance Dashboard</h1>
                    @isset($employee)
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-sm text-gray-600">For</span>
                        <span class="font-medium text-blue-600">{{ $employee->stage_name }}</span>
                        <span class="text-sm text-gray-500">| Last updated: {{ now()->format('M j, g:i a') }}</span>
                        @if($currentWeekSales > 25)
                            <span class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded-full">Top Performer</span>
                        @endif
                    </div>
                    @endisset
                </div>
                @isset($employee)
                <div class="flex gap-3">
                    <button onclick="exportReport()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
                @endisset
            </div>
        </div>

        <!-- Motivation Message -->
        @isset($employee)
        @if($motivation)
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 motivation-card">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Motivation Boost</h2>
            <p class="text-gray-700">{{ $motivation }}</p>
        </div>
        @endif
        @endisset

        <!-- Employee Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('divanj.predictive.report') }}" class="max-w-md">
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Select Employee</label>
                <div class="flex gap-2">
                    <select name="employee_id" id="employee_id" class="flex-grow rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Choose an employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(($employee->id ?? null) == $emp->id)>{{ $emp->stage_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        View Report
                    </button>
                </div>
            </form>
        </div>

        @isset($employee)
        <!-- Main Dashboard -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Performance Overview -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Performance Overview</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="performance-card bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Weekly Target</p>
                            <p class="text-2xl font-bold text-gray-900">25 cases</p>
                        </div>
                        <div class="performance-card bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Cases This Week</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $currentWeekSales }} cases</p>
                        </div>
                        <div class="performance-card bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Daily Target</p>
                            <p class="text-2xl font-bold {{ $dailyTarget >= 5 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $dailyTarget }} cases
                            </p>
                        </div>
                    </div>
                    
                    <!-- Sales Chart -->
                    <div class="chart-container">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>

                <!-- Weekday Predictions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Weekday Sales Plan</h2>
                        <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                            {{ count($weekdayPredictions['days']) }} days
                        </span>
                    </div>
                    
                    @if(isset($weekdayPredictions['error']))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <p class="text-red-700">{{ $weekdayPredictions['error'] }}</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Projected</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($weekdayPredictions['days'] as $day)
                                    <tr class="weekday-highlight">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $day['date'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $day['day'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $day['predicted'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dailyTarget }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $day['predicted'] >= $dailyTarget ? 'target-met' : 'target-missed' }}">
                                                {{ $day['predicted'] >= $dailyTarget ? 'On Track' : 'Focus Needed' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Strategy Tip:</strong> {{ $employee->stage_name }}, follow the script always to maximize results.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Recent Performance -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Performance</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Last 14 Days Sales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ array_sum(array_column($daywiseSales, 'total_qty')) }} cases</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Best Day</p>
                            <p class="text-lg font-medium text-gray-900">
                                @if($bestDay)
                                    {{ \Carbon\Carbon::parse($bestDay)->format('M j') }} - 
                                    <span class="text-blue-600">{{ $maxSale }} cases</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Average Daily Sales</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ count($daywiseSales) > 0 ? round(array_sum(array_column($daywiseSales, 'total_qty')) / count($daywiseSales), 1) : 0 }} cases
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Performing Products</h2>
                    <div class="space-y-3">
                        @forelse($topSellingProducts as $product => $quantity)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 truncate">{{ $product }}</span>
                            <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $quantity }} cases</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">No product data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Weekend Predictions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Weekend Opportunities</h2>
                        <span class="text-sm bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                            {{ count($weekendPredictions['days']) }} days
                        </span>
                    </div>
                    
                    @if(isset($weekendPredictions['error']))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4">
                            <p class="text-red-700">{{ $weekendPredictions['error'] }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($weekendPredictions['days'] as $day)
                            <div class="weekend-highlight bg-gray-50 p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $day['day'] }}, {{ $day['date'] }}</p>
                                        <p class="text-sm text-gray-600">Projected: {{ $day['predicted'] }} cases</p>
                                    </div>
                                    <span class="text-sm bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                                        {{ $day['predicted'] >= 5 ? 'Strong' : 'Moderate' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Weekend Tip:</strong> {{ $employee->stage_name }}, weekends often see different buying patterns - consider offering special bundles.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No employee selected</h3>
            <p class="mt-1 text-sm text-gray-500">Choose an employee from the dropdown to view their sales performance report.</p>
        </div>
        @endisset
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @isset($employee)
    // Sales Trend Chart
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    const salesTrendChart = new Chart(salesTrendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Cases Sold',
                data: {!! json_encode($chartData['quantities']) !!},
                backgroundColor: function(context) {
                    const value = context.raw;
                    return value >= 5 ? 'rgba(16, 185, 129, 0.7)' : 'rgba(239, 68, 68, 0.7)';
                },
                borderColor: function(context) {
                    const value = context.raw;
                    return value >= 5 ? 'rgba(16, 185, 129, 1)' : 'rgba(239, 68, 68, 1)';
                },
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} cases on ${context.label}`;
                        }
                    },
                    backgroundColor: '#1f2937',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    title: { 
                        display: true, 
                        text: 'Cases Sold',
                        font: { weight: 'bold' }
                    }
                },
                x: {
                    grid: { display: false },
                    title: { 
                        display: true,
                        text: 'Date',
                        font: { weight: 'bold' }
                    }
                }
            }
        }
    });

    // Export Report
    window.exportReport = function() {
        const csvRows = [];
        
        // Header
        csvRows.push('Sales Performance Report for {{ $employee->stage_name }}');
        csvRows.push(`Generated on,${new Date().toLocaleString()}`);
        csvRows.push('');
        
        // Performance Summary
        csvRows.push('Performance Summary');
        csvRows.push('Metric,Value');
        csvRows.push(`Weekly Target,25 cases`);
        csvRows.push(`Current Week Sales,{{ $currentWeekSales }} cases`);
        csvRows.push(`Daily Target,{{ $dailyTarget }} cases/day`);
        csvRows.push(`Last 14 Days Total,{{ array_sum(array_column($daywiseSales, 'total_qty')) }} cases`);
        csvRows.push('');
        
        // Recent Sales
        csvRows.push('Recent Sales (Last 14 Days)');
        csvRows.push('Date,Day,Cases Sold');
        @foreach($daywiseSales as $day)
        csvRows.push(`{{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }},{{ $day['day_name'] }},{{ $day['total_qty'] }}`);
        @endforeach
        csvRows.push('');
        
        // Weekday Predictions
        csvRows.push('Weekday Sales Plan');
        csvRows.push('Date,Day,Projected Cases,Daily Target,Status');
        @foreach($weekdayPredictions['days'] as $day)
        csvRows.push(`{{ $day['date'] }},{{ $day['day'] }},{{ $day['predicted'] }},{{ $dailyTarget }},{{ $day['predicted'] >= $dailyTarget ? 'On Track' : 'Focus Needed' }}`);
        @endforeach
        csvRows.push('');
        
        // Weekend Predictions
        csvRows.push('Weekend Opportunities');
        csvRows.push('Date,Day,Projected Cases');
        @foreach($weekendPredictions['days'] as $day)
        csvRows.push(`{{ $day['date'] }},{{ $day['day'] }},{{ $day['predicted'] }}`);
        @endforeach
        csvRows.push('');
        
        // Top Products
        csvRows.push('Top Performing Products');
        csvRows.push('Product,Cases Sold');
        @foreach($topSellingProducts as $product => $quantity)
        csvRows.push(`"{{ $product }}",{{ $quantity }}`);
        @endforeach
        
        // Motivation Message
        csvRows.push('');
        csvRows.push('Motivation Message');
        csvRows.push(`"{{ $motivation }}"`);
        
        // Create and download CSV
        const csvContent = csvRows.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Sales_Report_{{ $employee->stage_name }}_${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
    }
    @endisset
});
</script>
@endsection