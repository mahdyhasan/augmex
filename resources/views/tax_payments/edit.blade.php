@extends('layouts.app')

@section('title', 'Edit Tax Payment')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Tax Payment</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('tax_payments.update', $taxPayment->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="type" class="form-label">Tax Type</label>
                        <input type="text" class="form-control" id="type" name="type" value="{{ $taxPayment->type }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" value="{{ $taxPayment->amount }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ $taxPayment->payment_date }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Tax Payment</button>
                    <a href="{{ route('tax_payments.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
