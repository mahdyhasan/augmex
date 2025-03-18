@extends('layouts.app')

@section('title', 'Company Salary Sheet')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Company Salary Sheet</h3>
        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ route('payrolls.salary.sheet') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Select Month:</label>
                <input type="month" name="month" value="{{ request('month') }}" class="form-control" required>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                
                <a href="{{ route('payrolls.salary.sheet.export', ['format' => 'csv', 'month' => request('month')]) }}" class="btn btn-success">Export CSV</a>

                <a href="{{ route('payrolls.salary.sheet.export', ['format' => 'pdf', 'month' => request('month')]) }}" class="btn btn-danger">Export PDF</a>

                <a href="{{ route('payrolls.salary.sheet.print', ['month' => request('month')]) }}" target="_blank" class="btn btn-dark">
                    <i class="fas fa-print"></i> Print</a>

            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="salarySheetTable">
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
                    <th>Payment Date</th>
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
                        <td>{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('Y-m-d') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#salarySheetTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
