@extends('layouts.app')

@section('title', 'Client Payments')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Client Payments</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_client_payment">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Client Payment
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Explanation for users -->
                <div class="alert alert-secondary">
                    <h5>How to Use Client Payments?</h5>
                    <p>
                        This section helps track all payments received from clients.  
                        - View past payments, including invoice details, amount received, and payment methods.  
                        - Add a new payment using the "Add Client Payment" button.  
                        - Edit or delete existing payment records if necessary.  
                        - This data is essential for financial reporting and revenue tracking.
                    </p>
                </div>

                <!-- Client Payment Table -->
                <table id="clientPaymentTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice</th>
                            <th>Amount (Tk)</th>
                            <th>Payment Date</th>
                            <th>Method</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientPayments as $clientPayment)
                            <tr>
                                <td>{{ $clientPayment->id }}</td>
                                <td>Invoice #{{ $clientPayment->invoice_id }}</td>
                                <td>{{ number_format($clientPayment->amount) }}</td>
                                <td>{{ $clientPayment->payment_date }}</td>
                                <td>{{ $clientPayment->method }}</td>
                                <td>
                                    <a href="{{ route('client_payments.edit', $clientPayment->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('client_payments.destroy', $clientPayment->id) }}" method="POST" class="d-inline">
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

<!-- Offcanvas: Add Client Payment -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_client_payment">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Client Payment</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('client_payments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="invoice_id" class="form-label">Invoice</label>
                <select class="form-control" id="invoice_id" name="invoice_id" required>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}">Invoice #{{ $invoice->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
            </div>

            <div class="mb-3">
                <label for="method" class="form-label">Payment Method</label>
                <input type="text" class="form-control" id="method" name="method" required placeholder="e.g., Bank Transfer, Cash">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Client Payment</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
