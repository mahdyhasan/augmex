@extends('layouts.pdf')

@section('content')
<h2>Salary Sheet for {{ $month->format('F Y') }}</h2>
<table class="table">
    <thead>
        <tr>
            <th>Employee</th>
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
                <td>{{ $payroll->base_salary }}</td>
                <td>{{ $payroll->bonuses }}</td>
                <td>{{ $payroll->commission }}</td>
                <td>{{ $payroll->transport }}</td>
                <td>{{ $payroll->others }}</td>
                <td>{{ $payroll->deductions }}</td>
                <td>{{ $payroll->net_salary }}</td>
                <td>{{ ucfirst($payroll->payment_status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
