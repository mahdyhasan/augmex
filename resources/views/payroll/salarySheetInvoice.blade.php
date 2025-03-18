@extends('layouts.invoice')

@section('content')
<div class="container my-4 p-4 border rounded bg-white">
    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-primary">Company Salary Sheet</h2>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    </div>

    <p><strong>Month:</strong> {{ $month->format('F Y') }}</p>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Employee Name</th>
                <th>Position</th>
                <th>Base Salary</th>
                <th>Bonuses</th>
                <th>Commission</th>
                <th>Transport</th>
                <th>Others</th>
                <th>Deductions</th>
                <th>Net Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->employee->user->name ?? 'N/A' }}</td>
                    <td>{{ $payroll->employee->position ?? '-' }}</td>
                    <td>{{ number_format($payroll->base_salary, 2) }}</td>
                    <td>{{ number_format($payroll->bonuses, 2) }}</td>
                    <td>{{ number_format($payroll->commission, 2) }}</td>
                    <td>{{ number_format($payroll->transport, 2) }}</td>
                    <td>{{ number_format($payroll->others, 2) }}</td>
                    <td class="text-danger">{{ number_format($payroll->deductions, 2) }}</td>
                    <td class="text-success">{{ number_format($payroll->net_salary, 2) }}</td>
                    <td>
                        <span class="badge {{ $payroll->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($payroll->payment_status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center mt-4">
        <p><strong>Authorized by:</strong> ___________________</p>
        <p>HR / Accounts Department</p>
    </div>
</div>
@endsection
