@extends('layouts.app')

@section('title', 'Liabilities')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Liabilities</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_liability">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Liability
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Amount</th>
                            <th>Interest Rate (%)</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($liabilities as $liability)
                            <tr>
                                <td>{{ $liability->account->name }}</td>
                                <td>{{ $liability->amount }}</td>
                                <td>{{ $liability->interest_rate }}%</td>
                                <td>{{ $liability->start_date }}</td>
                                <td>{{ $liability->due_date }}</td>
                                <td>{{ ucfirst($liability->status) }}</td>
                                <td>
                                    <a href="{{ route('liabilities.edit', $liability->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('liabilities.destroy', $liability->id) }}" method="POST" class="d-inline">
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

<!-- Offcanvas: Add Liability -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_liability">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Liability</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('liabilities.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="account_id" class="form-label">Account</label>
                <select class="form-control" id="account_id" name="account_id" required>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                <input type="number" class="form-control" id="interest_rate" name="interest_rate" step="0.01" min="0" max="100">
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="closed">Closed</option>
                    <option value="defaulted">Defaulted</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Liability</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
