@extends('layouts.app')

@section('title', 'Salary Sheet')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Title -->
                <h3 class="mb-0">Salary Sheet</h3>
                
                <!-- Action Buttons Group -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- Month Filter -->
                    <form action="{{ route('payrolls.salary.sheet') }}" method="GET" class="d-flex">
                        <input type="month" name="month" class="form-control me-2" 
                            value="{{ request('month', \Carbon\Carbon::now()->format('Y-m')) }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                    
                    <!-- Export Options -->
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form action="{{ route('payrolls.salary.sheet.export') }}" method="GET" class="px-2 py-1">
                                    <input type="hidden" name="month" value="{{ request('month') }}">
                                    <input type="hidden" name="format" value="pdf">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('payrolls.salary.sheet.export') }}" method="GET" class="px-2 py-1">
                                    <input type="hidden" name="month" value="{{ request('month') }}">
                                    <input type="hidden" name="format" value="csv">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-file-csv text-success me-2"></i> CSV
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Cash Signature Sheet -->
                    <div class="dropdown">
                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-signature"></i> Signature Sheet
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('payrolls.cash.signature', ['month' => request('month'), 'format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('payrolls.cash.signature', ['month' => request('month'), 'format' => 'csv']) }}">
                                    <i class="fas fa-file-csv text-success me-2"></i> CSV
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
            <div class="card-body">
                @if($payrolls->isEmpty())
                    <div class="alert alert-info">No payroll data found for the selected period.</div>
                @else
                    <div class="alert alert-info">
                        Showing payroll for period: 
                        {{ \Carbon\Carbon::parse($payrolls->first()->pay_period_start)->format('d M Y') }} to 
                        {{ \Carbon\Carbon::parse($payrolls->first()->pay_period_end)->format('d M Y') }}
                    </div>
                    
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Sl</th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Gross Salary</th>
                                <th>Transport</th>
                                <th>Commission</th>
                                <th>Bonus</th>
                                <th>Total Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $index => $payroll)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $payroll->employee->user->name ?? 'N/A' }}</td>
                                <td>{{ $payroll->employee->department ?? '-' }}</td>
                                <td>{{ $payroll->employee->position ?? '-' }}</td>
                                <td class="text-right">{{ number_format($payroll->base_salary, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->transport, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->commission, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->bonuses, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->base_salary + $payroll->transport + $payroll->commission + $payroll->bonuses, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->deductions, 2) }}</td>
                                <td class="text-right">{{ number_format($payroll->net_salary, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $payroll->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payroll->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection