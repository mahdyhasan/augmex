@extends('layouts.app')

@section('title', 'Payroll')

@section('content')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Payroll Management</h3>

                <a href="{{ route('payrolls.salary.sheet') }}" class="btn btn-info">
                    <i class="fas fa-file-invoice-dollar"></i> Salary Sheet
                </a>


                <button class="btn btn-primary" onclick="openGeneratePayroll()">
                    <i class="fas fa-plus-circle"></i> Generate Payroll
                </button>
            </div>



    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Employee</th>
                <th>Month</th>
                <th>Base Salary</th>
                <th>Bonuses</th>
                <th>Commission</th>
                <th>Transport</th>
                <th>Others</th>
                <th>Deductions</th>
                <th>Net Salary</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payrolls as $payroll)
                <tr>
                    <td>
                        @if($payroll->employee)
                            {{ $payroll->employee->user->name ?? 'N/A' }}
                        @else
                            <span class="text-danger">No Employee</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('F Y') }}</td>
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
                    <td>    
                        {{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td>
                        @if($payroll->payment_status == 'pending')
                            <form action="{{ route('payrolls.pay', $payroll->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check-circle"></i> Mark as Paid
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Offcanvas for Generating Payroll -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="generatePayrollCanvas">
    <div class="offcanvas-header">
        <h5>Generate Payroll</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="generatePayrollForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-control" required>
                    <option value="">Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Month & Year</label>
                <input type="month" name="month" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Bonuses</label>
                <input type="number" name="bonuses" class="form-control" min="0" step="0.01">
            </div>

            <div class="mb-3">
                <label class="form-label">Commission</label>
                <input type="number" name="commission" class="form-control" min="0" step="0.01">
            </div>

            <div class="mb-3">
                <label class="form-label">Transport Allowance</label>
                <input type="number" name="transport" class="form-control" min="0" step="0.01">
            </div>

            <div class="mb-3">
                <label class="form-label">others</label>
                <input type="number" name="others" class="form-control" min="0" step="0.01">
            </div>

            <button type="submit" class="btn btn-primary w-100">Generate Payroll</button>
        </form>
    </div>
</div>

@endsection

@section('js')

<script>
function openGeneratePayroll() {
    var offcanvas = new bootstrap.Offcanvas(document.getElementById('generatePayrollCanvas'));
    offcanvas.show();
}

document.getElementById('generatePayrollForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('{{ route("payrolls.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(new FormData(this)))
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endsection
