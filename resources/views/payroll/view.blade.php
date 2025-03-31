<div class="payroll-details">
    <div class="d-flex align-items-center mb-4">
        <div class="avatar me-3">
            <img src="{{ $payroll->employee->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                 class="rounded-circle" width="60" height="60">
        </div>
        <div>
            <h4>{{ $payroll->employee->user->name }}</h4>
            <p class="text-muted mb-0">{{ $payroll->employee->designation->name ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Pay Period:</strong>
            <p>{{ $payroll->pay_period_start->format('d M Y') }} - {{ $payroll->pay_period_end->format('d M Y') }}</p>
        </div>
        <div class="col-md-6">
            <strong>Status:</strong>
            <span class="badge bg-{{ $payroll->payment_status == 'paid' ? 'success' : 'warning' }}">
                {{ ucfirst($payroll->payment_status) }}
            </span>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0">Salary Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Base Salary:</strong> {{ number_format($payroll->base_salary, 2) }} BDT</p>
                    <p><strong>Bonuses:</strong> {{ number_format($payroll->bonuses, 2) }} BDT</p>
                    <p><strong>Commission:</strong> {{ number_format($payroll->commission, 2) }} BDT</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Transport:</strong> {{ number_format($payroll->transport, 2) }} BDT</p>
                    <p><strong>Other Allowances:</strong> {{ number_format($payroll->others, 2) }} BDT</p>
                    <p><strong>Deductions:</strong> -{{ number_format($payroll->deductions, 2) }} BDT</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-success">
        <h5 class="mb-0">Net Salary: {{ number_format($payroll->net_salary, 2) }} BDT</h5>
    </div>

    @if($payroll->payment_status == 'paid' && $payroll->payment_date)
        <p class="text-muted">Paid on: {{ $payroll->payment_date->format('d M Y') }}</p>
    @endif
</div>