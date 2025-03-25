@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard')

@section('content')
<style>
    .dashboard-card {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        text-align: center;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #e9ecef;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .dashboard-card i {
        font-size: 30px;
        margin-bottom: 10px;
        color: #6c757d;
    }
    .card-title {
        font-size: 16px;
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    .card-value {
        font-size: 22px;
        font-weight: 600;
        color: #343a40;
    }
    .card-header {
        background: #f8f9fa;
        color: #495057;
        border-radius: 10px 10px 0 0;
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    .card-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 20px;
    }
    .card-header p {
        margin: 0;
        font-size: 13px;
        opacity: 0.8;
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .table thead {
        background: #f8f9fa;
        color: #495057;
    }
    .table th, .table td {
        padding: 10px;
        font-size: 14px;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .date-range-filter {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .date-range-filter input {
        padding: 8px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
    }
    .date-range-filter button {
        padding: 8px 15px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .date-range-filter button:hover {
        background: #0056b3;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>👋 Welcome Back, SuperAdmin!</h2>
                <p>Here’s an overview of company performance.</p>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="date-range-filter">
            <form action="{{ route('dashboard') }}" method="GET">
                <input type="date" name="start_date" value="{{ $startDate }}">
                <input type="date" name="end_date" value="{{ $endDate }}">
                <button type="submit">Apply Filter</button>
            </form>
        </div>



        <!-- Stats Cards -->
        <div class="row mb-4">
            <!-- Total Employees -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-users"></i>
                    <h5 class="card-title">Total Employees</h5>
                    <h2 class="card-value">{{ $totalEmployees }}</h2>
                </div>
            </div>

            <!-- Total Clients -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-building"></i>
                    <h5 class="card-title">Total Clients</h5>
                    <h2 class="card-value">{{ $totalClients }}</h2>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-credit-card"></i>
                    <h5 class="card-title">Total Transactions</h5>
                    <h2 class="card-value">{{ $totalTransactions }}</h2>
                </div>
            </div>
        </div>



        <!-- Additional Stats Cards -->
        <div class="row mb-4">
            <!-- Total Expenses -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-cash"></i>
                    <h5 class="card-title">Total Expenses</h5>
                    <h2 class="card-value">{{ number_format($totalExpenses, 2) }} Tk</h2>
                </div>
            </div>

            <!-- Bank Balance -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-pig-money"></i>
                    <h5 class="card-title">Bank Balance</h5>
                    <h2 class="card-value">{{ number_format($bankBalance, 2) }} Tk</h2>
                </div>
            </div>
            <!-- Total Earnings -->
            <div class="col-xl-4 col-sm-6 mb-4">
                <div class="dashboard-card">
                    <i class="ti ti-wallet"></i>
                    <h5 class="card-title">Total Earnings</h5>
                    <h2 class="card-value">{{ number_format($totalEarnings, 2) }} Tk</h2>
                </div>
            </div>
        </div>

        <!-- Tables -->
        <div class="row">
            <!-- Recent Transactions -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount (Tk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                    <td>{{ ucfirst($transaction->type) }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Invoices</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Amount (Tk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_date }}</td>
                                    <td>{{ $invoice->client->company }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Tables -->
        <div class="row">
            <!-- Monthly Earnings -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Earnings</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Earnings (Tk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyEarnings as $month => $earnings)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</td>
                                    <td>{{ number_format($earnings, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Monthly Expenses -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Expenses</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Expenses (Tk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyExpenses as $month => $expenses)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</td>
                                    <td>{{ number_format($expenses, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

       

            <!-- Expense Summary -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Expense Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount (Tk)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenseSummary['expensesByCategory'] as $expense)
                                <tr>
                                    <td>{{ $expense->expenseCategory->name }}</td>
                                    <td>{{ number_format($expense->total, 2) }}</td>
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
@endsection