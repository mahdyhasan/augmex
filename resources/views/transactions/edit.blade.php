@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Transaction</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Account Selection -->
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select class="form-control" id="account_id" name="account_id" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $transaction->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Selection -->
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="Deposit" {{ $transaction->type == 'Deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="Withdrawal" {{ $transaction->type == 'Withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="{{ $transaction->amount }}" required>
                    </div>

                    <!-- Reference -->
                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" class="form-control" id="reference" name="reference" value="{{ $transaction->reference }}">
                    </div>

                    <!-- Transaction Date -->
                    <div class="mb-3">
                        <label for="transaction_date" class="form-label">Transaction Date</label>
                        <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ $transaction->transaction_date }}" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2">{{ $transaction->description }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Transaction</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
