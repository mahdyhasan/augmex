@extends('layouts.app')

@section('title', 'Edit Payroll')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Payroll for {{ $payroll->employee->user->name ?? 'N/A' }}</h2>

    <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Month & Year</label>
                    <input type="month" name="month" class="form-control" value="{{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('Y-m') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Base Salary</label>
                    <input type="number" name="base_salary" class="form-control" value="{{ $payroll->base_salary }}" min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bonuses</label>
                    <input type="number" name="bonuses" class="form-control" value="{{ $payroll->bonuses }}" min="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Commission</label>
                    <input type="number" name="commission" class="form-control" value="{{ $payroll->commission }}" min="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Transport Allowance</label>
                    <input type="number" name="transport" class="form-control" value="{{ $payroll->transport }}" min="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Others</label>
                    <input type="number" name="others" class="form-control" value="{{ $payroll->others }}" min="0" step="0.01">
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Deductions</label>
                    <input type="number" name="deductions" class="form-control text-danger" value="{{ $payroll->deductions }}" min="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label class="form-label">Net Salary</label>
                    <input type="number" name="net_salary" class="form-control text-success" value="{{ $payroll->net_salary }}" min="0" step="0.01" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-control">
                        <option value="pending" {{ $payroll->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $payroll->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ $payroll->payment_date }}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3">{{ $payroll->remarks }}</textarea>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">Update Payroll</button>
            <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
