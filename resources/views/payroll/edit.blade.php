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

                    <!-- Earnings Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Earnings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Base Salary</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="base_salary" class="form-control salary-input" 
                                               value="{{ $payroll->base_salary }}" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bonuses</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="bonuses" class="form-control salary-input" 
                                               value="{{ $payroll->bonuses }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Commission</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="commission" class="form-control salary-input" 
                                               value="{{ $payroll->commission }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Transport Allowance</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="transport" class="form-control salary-input" 
                                               value="{{ $payroll->transport }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Other Allowances</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="others" class="form-control salary-input" 
                                               value="{{ $payroll->others }}" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <h5 class="mb-0">Total Earnings: <span id="totalEarnings" class="text-success">{{ number_format($payroll->base_salary + $payroll->bonuses + $payroll->commission + $payroll->transport + $payroll->others, 2) }}</span> BDT</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Deductions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Deductions</label>
                                    <div class="input-group">
                                        <span class="input-group-text">BDT</span>
                                        <input type="number" name="deductions" class="form-control salary-input" 
                                               value="{{ $payroll->deductions }}" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <h5 class="mb-0">Total Deductions: <span id="totalDeductions" class="text-danger">{{ number_format($payroll->deductions, 2) }}</span> BDT</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-control">
                                        <option value="pending" {{ $payroll->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $payroll->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" 
                                           value="{{ $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Salary Calculation -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Net Salary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="calculation-breakdown" id="calculationBreakdown">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Base Salary:</span>
                                            <span id="baseSalaryDisplay">{{ number_format($payroll->base_salary, 2) }} BDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Bonuses:</span>
                                            <span id="bonusesDisplay">+ {{ number_format($payroll->bonuses, 2) }} BDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Commission:</span>
                                            <span id="commissionDisplay">+ {{ number_format($payroll->commission, 2) }} BDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Transport Allowance:</span>
                                            <span id="transportDisplay">+ {{ number_format($payroll->transport, 2) }} BDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Other Allowances:</span>
                                            <span id="othersDisplay">+ {{ number_format($payroll->others, 2) }} BDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Deductions:</span>
                                            <span id="deductionsDisplay">- {{ number_format($payroll->deductions, 2) }} BDT</span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h2 class="text-success mb-0" id="netSalaryDisplay">{{ number_format($payroll->net_salary, 2) }} BDT</h2>
                                    <small class="text-muted">Net Payable Amount</small>
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
    $(document).ready(function() {
        // Format number with commas
        function formatNumber(num) {
            return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Calculate all values
        function calculateAll() {
            // Get all input values
            const baseSalary = parseFloat($("input[name='base_salary']").val()) || 0;
            const bonuses = parseFloat($("input[name='bonuses']").val()) || 0;
            const commission = parseFloat($("input[name='commission']").val()) || 0;
            const transport = parseFloat($("input[name='transport']").val()) || 0;
            const others = parseFloat($("input[name='others']").val()) || 0;
            const deductions = parseFloat($("input[name='deductions']").val()) || 0;

            // Calculate totals
            const totalEarnings = baseSalary + bonuses + commission + transport + others;
            const netSalary = totalEarnings - deductions;

            // Update displays
            $("#totalEarnings").text(formatNumber(totalEarnings));
            $("#totalDeductions").text(formatNumber(deductions));
            
            // Update breakdown
            $("#baseSalaryDisplay").text(formatNumber(baseSalary) + " BDT");
            $("#bonusesDisplay").text("+ " + formatNumber(bonuses) + " BDT");
            $("#commissionDisplay").text("+ " + formatNumber(commission) + " BDT");
            $("#transportDisplay").text("+ " + formatNumber(transport) + " BDT");
            $("#othersDisplay").text("+ " + formatNumber(others) + " BDT");
            $("#deductionsDisplay").text("- " + formatNumber(deductions) + " BDT");
            
            // Update net salary - FIXED THIS LINE
            $("#netSalaryDisplay").text(formatNumber(netSalary) + " BDT");
        }

        // Calculate on page load
        calculateAll();

        // Calculate on any input change with debounce
        let timeout;
        $(".salary-input").on('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(calculateAll, 100);
        });

        // Also calculate when fields lose focus
        $(".salary-input").on('blur', function() {
            // Format the input value
            if ($(this).val()) {
                $(this).val(parseFloat($(this).val()).toFixed(2));
            }
            calculateAll();
        });
        
        // Format numbers when page loads
        $(".salary-input").each(function() {
            if ($(this).val()) {
                $(this).val(parseFloat($(this).val()).toFixed(2));
            }
        });
    });
</script>
@endsection