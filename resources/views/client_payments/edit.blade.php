@extends('layouts.app')

@section('title', 'Edit Client Payment')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Client Payment</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('client_payments.update', $clientPayment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Invoice Selection -->
                    <div class="mb-3">
                        <label for="invoice_id" class="form-label">Invoice</label>
                        <select class="form-control" id="invoice_id" name="invoice_id" required>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" {{ $clientPayment->invoice_id == $invoice->id ? 'selected' : '' }}>
                                    Invoice #{{ $invoice->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="{{ $clientPayment->amount }}" required>
                    </div>

                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ $clientPayment->payment_date }}" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="method" class="form-label">Payment Method</label>
                        <input type="text" class="form-control" id="method" name="method" value="{{ $clientPayment->method }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Payment</button>
                    <a href="{{ route('client_payments.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
