@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<style>
    .offcanvas {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        height: 60vh; 
    }
    .offcanvas-header {
        background: #007bff;
        color: white;
    }
    .offcanvas-body {
        padding: 20px;
    }
    .summary-table {
        width: 100%;
        margin-bottom: 20px;
    }
    .summary-table th, .summary-table td {
        padding: 8px;
        border-bottom: 1px solid #e9ecef;
    }
    .summary-table th {
        background: #f8f9fa;
    }
    .toggle-summary-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Sales Report</h3>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form action="{{ route('sales_report.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="employee_id">Employee</label>
                            <select name="employee_id" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->user->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="client_id">Client</label>
                            <select name="client_id" class="form-control">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary mt-4">Filter</button>
                            <a href="{{ route('sales_report.index') }}" class="btn btn-secondary mt-4">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Sales Report Table -->
                @if(session('success'))
                    <div class="alert alert-success mt-4">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Client</th>
                            <th>Sales Quantity</th>
                            <th>Sales Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->date }}</td>
                                <td>
                                    @if($sale->employee && $sale->employee->user)
                                        {{ $sale->employee->user->name }}
                                    @else
                                        <span class="text-danger">N/A</span>
                                    @endif
                                </td>                                
                                <td>{{ $sale->client->company ?? 'N/A' }}</td>
                                <td>{{ $sale->sales_qty }}</td>
                                <td>{{ $sale->sales_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>





@endsection

@section('script')
<!-- Bootstrap JS for Offcanvas -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
