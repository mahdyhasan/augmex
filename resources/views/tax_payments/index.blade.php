@extends('layouts.app')

@section('title', 'Tax Payments')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Tax Payments</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_tax_payment">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Tax Payment
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Explanation for users -->
                <div class="alert alert-secondary">
                    <h5>How to Use Tax Payments?</h5>
                    <p>
                        This section helps track all tax payments made by the company.  
                        - View past tax payments, including VAT, corporate tax, and payroll tax.  
                        - Add a new tax payment using the "Add Tax Payment" button.  
                        - Edit or delete existing tax records to ensure compliance.  
                        - Keeping tax payments updated helps with financial audits and compliance.
                    </p>
                </div>

                <!-- Tax Payment Table -->
                <table id="taxPaymentTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Amount (Tk)</th>
                            <th>Payment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxPayments as $taxPayment)
                            <tr>
                                <td>{{ $taxPayment->id }}</td>
                                <td>{{ $taxPayment->type }}</td>
                                <td>{{ number_format($taxPayment->amount) }}</td>
                                <td>{{ $taxPayment->payment_date }}</td>
                                <td>
                                    <a href="{{ route('tax_payments.edit', $taxPayment->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('tax_payments.destroy', $taxPayment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Offcanvas: Add Tax Payment -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_tax_payment">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Tax Payment</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('tax_payments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="type" class="form-label">Tax Type</label>
                <input type="text" class="form-control" id="type" name="type" required placeholder="e.g., Corporate Tax, VAT">
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Tax Payment</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
