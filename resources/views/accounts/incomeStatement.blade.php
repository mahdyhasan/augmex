@extends('layouts.app')

@section('title', 'Income Statement')

@section('content')
<div class="container">
    <h2 class="mb-4">Income Statement</h2>

    <!-- Date Range Filter -->
    <form action="{{ route('accounts.incomeStatement') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-md-4">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </div>
    </form>

    <!-- Display Income Statement -->
    @if(isset($revenues))
    <div class="card">
        <div class="card-header text-center bg-info text-white">
            <h4>Income Statement ({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</h4>
        </div>
        <div class="card-body">

            <!-- Revenue Section -->
            <h5 class="text-primary">Revenue</h5>
            <table class="table">
                <tr>
                    <td><strong>Total Revenues (BDT)</strong></td>
                    <td class="text-end"><strong>{{ number_format($revenues, 2) }}</strong></td>
                </tr>
            </table>

            <h5 class="text-danger mt-4">Expenses</h5>
                <table class="table">
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense['category'] }}</td>
                        <td class="text-end">{{ number_format($expense['total_amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light">
                        <td><strong>Total Expenses</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalExpenses, 2) }}</strong></td>
                    </tr>
            </table>
            <!-- Net Income Section -->
            <h4 class="text-center mt-4">
                <strong>Net Income: </strong> 
                <span class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($netIncome, 2) }}
                </span>
            </h4>
        </div>
    </div>
    @endif
</div>
@endsection
