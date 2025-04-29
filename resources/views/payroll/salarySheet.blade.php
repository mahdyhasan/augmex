@extends('layouts.app')

@section('title', 'Salary Sheet')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h3 class="mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            Salary Sheet
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-end flex-wrap gap-3">
                            <!-- Month Filter Form -->
                            <form action="{{ route('payrolls.salary.sheet') }}" method="GET" class="d-flex">
                                <div class="input-group" style="max-width: 250px;">
                                    <input type="month" name="month" class="form-control border-end-0" 
                                        value="{{ request('month', \Carbon\Carbon::now()->format('Y-m')) }}"
                                        onchange="this.form.submit()">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Export Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <h6 class="dropdown-header">Export Options</h6>
                                    </li>
                                    <li>
                                        <form action="{{ route('payrolls.salary.sheet.export') }}" method="GET">
                                            <input type="hidden" name="month" value="{{ request('month') }}">
                                            <input type="hidden" name="format" value="pdf">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-file-pdf text-danger me-2"></i> PDF Report
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('payrolls.salary.sheet.export') }}" method="GET">
                                            <input type="hidden" name="month" value="{{ request('month') }}">
                                            <input type="hidden" name="format" value="csv">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-file-csv text-success me-2"></i> CSV Data
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <h6 class="dropdown-header">Signature Sheets</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('payrolls.cash.signature', ['month' => request('month'), 'format' => 'pdf']) }}">
                                            <i class="fas fa-signature text-info me-2"></i> PDF Signature Sheet
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('payrolls.cash.signature', ['month' => request('month'), 'format' => 'csv']) }}">
                                            <i class="fas fa-signature text-info me-2"></i> CSV Signature Sheet
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any()))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($payrolls->isEmpty()))
                    <div class="alert alert-info">No payroll data found for the selected period.</div>
                @else
                    <div class="alert alert-info">
                        Showing payroll for period: 
                        {{ \Carbon\Carbon::parse($payrolls->first()->pay_period_start)->format('d M Y') }} to 
                        {{ \Carbon\Carbon::parse($payrolls->first()->pay_period_end)->format('d M Y') }}
                    </div>

                    <form id="salaryForm" action="{{ route('payrolls.update.salary.sheet') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="month" value="{{ request('month') }}">
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
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right" 
                                            name="payrolls[{{ $payroll->id }}][base_salary]" 
                                            value="{{ $payroll->base_salary }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right" 
                                            name="payrolls[{{ $payroll->id }}][transport]" 
                                            value="{{ $payroll->transport }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right" 
                                            name="payrolls[{{ $payroll->id }}][commission]" 
                                            value="{{ $payroll->commission }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right" 
                                            name="payrolls[{{ $payroll->id }}][bonuses]" 
                                            value="{{ $payroll->bonuses }}" required>
                                    </td>
                                    <td class="text-right total-salary">
                                        {{ number_format($payroll->base_salary + $payroll->transport + $payroll->commission + $payroll->bonuses, 2) }}
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right" 
                                            name="payrolls[{{ $payroll->id }}][deductions]" 
                                            value="{{ $payroll->deductions }}" required>
                                    </td>
                                    <td class="text-right net-salary">
                                        {{ number_format($payroll->net_salary, 2) }}
                                        <input type="hidden" name="payrolls[{{ $payroll->id }}][net_salary]" 
                                            value="{{ $payroll->net_salary }}" class="net-salary-input">
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm" 
                                            name="payrolls[{{ $payroll->id }}][payment_status]" required>
                                            <option value="paid" {{ $payroll->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="pending" {{ $payroll->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Submit Changes
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calculations on page load
        document.querySelectorAll('tr').forEach(row => {
            updateRowCalculations(row);
        });

        // Update on input change
        document.querySelectorAll('input[name^="payrolls"]').forEach(input => {
            input.addEventListener('input', function() {
                updateRowCalculations(this.closest('tr'));
            });
        });

        function updateRowCalculations(row) {
            const payrollId = row.querySelector('input[name^="payrolls"]')?.name?.match(/payrolls\[(\d+)\]/)?.[1];
            if (!payrollId) return;

            const baseSalary = parseFloat(row.querySelector(`input[name="payrolls[${payrollId}][base_salary]"]`).value) || 0;
            const transport = parseFloat(row.querySelector(`input[name="payrolls[${payrollId}][transport]"]`).value) || 0;
            const commission = parseFloat(row.querySelector(`input[name="payrolls[${payrollId}][commission]"]`).value) || 0;
            const bonuses = parseFloat(row.querySelector(`input[name="payrolls[${payrollId}][bonuses]"]`).value) || 0;
            const deductions = parseFloat(row.querySelector(`input[name="payrolls[${payrollId}][deductions]"]`).value) || 0;
            
            const totalSalary = baseSalary + transport + commission + bonuses;
            const netSalary = totalSalary - deductions;
            
            row.querySelector('.total-salary').textContent = totalSalary.toFixed(2);
            row.querySelector('.net-salary').textContent = netSalary.toFixed(2);
            const netSalaryInput = row.querySelector(`input[name="payrolls[${payrollId}][net_salary]"]`);
            if (netSalaryInput) netSalaryInput.value = netSalary.toFixed(2);
        }

        document.getElementById('salaryForm')?.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to save all changes?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection