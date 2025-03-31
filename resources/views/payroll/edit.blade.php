@extends('layouts.app')

@section('title', 'Edit Payroll')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Edit Payroll</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control" value="{{ $payroll->employee->user->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pay Period</label>
                            <input type="text" class="form-control" 
                                   value="{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('d M Y') }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Base Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="base_salary" class="form-control salary-input" 
                                       value="{{ $payroll->base_salary }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bonuses</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="bonuses" class="form-control salary-input" 
                                       value="{{ $payroll->bonuses }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Commission</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="commission" class="form-control salary-input" 
                                       value="{{ $payroll->commission }}" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Transport Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="transport" class="form-control salary-input" 
                                       value="{{ $payroll->transport }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Other Allowances</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="others" class="form-control salary-input" 
                                       value="{{ $payroll->others }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deductions</label>
                            <div class="input-group">
                                <span class="input-group-text">BDT</span>
                                <input type="number" name="deductions" class="form-control salary-input" 
                                       value="{{ $payroll->deductions }}" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="pending" {{ $payroll->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $payroll->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" 
                                   value="{{ $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    <!-- Net Salary Calculation Display -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Net Salary Calculation</h5>
                                        <h3 class="mb-0 text-success" id="netSalaryDisplay">
                                            {{ number_format($payroll->net_salary, 2) }} BDT
                                        </h3>
                                    </div>
                                    <div class="calculation-breakdown mt-2 text-muted small" id="calculationBreakdown"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function calculateNetSalary() {
        // Get all input values
        const baseSalary = parseFloat($("input[name='base_salary']").val()) || 0;
        const bonuses = parseFloat($("input[name='bonuses']").val()) || 0;
        const commission = parseFloat($("input[name='commission']").val()) || 0;
        const transport = parseFloat($("input[name='transport']").val()) || 0;
        const others = parseFloat($("input[name='others']").val()) || 0;
        const deductions = parseFloat($("input[name='deductions']").val()) || 0;

        // Calculate components
        const additions = baseSalary + bonuses + commission + transport + others;
        const netSalary = additions - deductions;

        // Update display
        $("#netSalaryDisplay").text(netSalary.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + " BDT");

        // Show calculation breakdown
        const breakdown = `
            Base Salary: ${baseSalary.toFixed(2)} BDT<br>
            + Bonuses: ${bonuses.toFixed(2)} BDT<br>
            + Commission: ${commission.toFixed(2)} BDT<br>
            + Transport: ${transport.toFixed(2)} BDT<br>
            + Others: ${others.toFixed(2)} BDT<br>
            - Deductions: ${deductions.toFixed(2)} BDT<br>
            <strong>Total: ${netSalary.toFixed(2)} BDT</strong>
        `;
        $("#calculationBreakdown").html(breakdown);
    }

    $(document).ready(function() {
        // Calculate on page load
        calculateNetSalary();

        // Calculate on any input change with debounce
        let timeout;
        $(".salary-input").on('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(calculateNetSalary, 300);
        });

        // Also calculate when fields lose focus
        $(".salary-input").on('blur', calculateNetSalary);
    });
</script>
@endsection