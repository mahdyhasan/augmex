@extends('layouts.app')

@section('title', 'Edit Liability')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Liability</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('liabilities.update', $liability->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Account Selection -->
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Account</label>
                        <select class="form-control" id="account_id" name="account_id" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $liability->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="{{ $liability->amount }}" required>
                    </div>

                    <!-- Interest Rate -->
                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                        <input type="number" class="form-control" id="interest_rate" name="interest_rate" step="0.01" min="0" max="100" value="{{ $liability->interest_rate }}">
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $liability->start_date }}" required>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ $liability->due_date }}" required>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" {{ $liability->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="closed" {{ $liability->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="defaulted" {{ $liability->status == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Liability</button>
                    <a href="{{ route('liabilities.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
