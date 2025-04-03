@extends('layouts.app')

@section('title', 'Edit Client Payment')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card col-md-4">
            <div class="card-header">
                <h3>Edit Client Payment</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('client_payments.update', $clientPayment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Invoice Selection -->
                    <div class="mb-3">
                        <!-- <label for="invoice_id" class="form-label">Invoice</label>
                        <select class="form-control" id="invoice_id" name="invoice_id" required>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" {{ $clientPayment->invoice_id == $invoice->id ? 'selected' : '' }}>
                                    Invoice #{{ $invoice->id }} - {{$invoice->invoice_no}}
                                </option>
                            @endforeach
                        </select> -->
                        <label class="form-label">Invoice</label>
                        <div class="form-control-plaintext">
                            Invoice #{{ $clientPayment->invoice->id }} - {{ $clientPayment->invoice->invoice_no }}
                        </div>
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
                        <select class="form-select" id="method" name="method" required>
                            <option value="" disabled {{ !$clientPayment->method ? 'selected' : '' }}>Select payment method</option>
                            <option value="Bank Transfer" {{ $clientPayment->method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="PayPal" {{ $clientPayment->method == 'PayPal' ? 'selected' : '' }}>PayPal</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Payment</button>
                    <a href="{{ route('client_payments.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
