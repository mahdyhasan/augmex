@extends('layouts.app')

@section('title', 'Bank Accounts')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Bank Accounts</h3>
                    <a href="javascript:void(0);" class="btn btn-primary"
                       data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_bank">
                       <i class="ti ti-square-rounded-plus me-2"></i>Add Bank Account
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Account Number</th>
                                <th>Balance</th>
                                <th>Currency</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bankAccounts as $bankAccount)
                                <tr>
                                    <td>{{ $bankAccount->id }}</td>
                                    <td>{{ $bankAccount->name }}</td>
                                    <td>{{ $bankAccount->account_number }}</td>
                                    <td>{{ $bankAccount->balance }}</td>
                                    <td>{{ $bankAccount->currency }}</td>
                                    <td>
                                        <a href="{{ route('bank_accounts.edit', $bankAccount->id) }}" class="btn btn-warning">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Offcanvas: Add Bank -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_bank">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Bank Account</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('bank_accounts.store') }}" method="POST">
            @csrf

            <!-- Account Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Account Name</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter account name">
            </div>

            <!-- Account Number -->
            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" class="form-control" id="account_number" name="account_number" required placeholder="Enter account number">
            </div>

            <!-- Balance -->
            <div class="mb-3">
                <label for="balance" class="form-label">Balance</label>
                <input type="number" class="form-control" id="balance" name="balance" step="0.01" required placeholder="Enter initial balance">
            </div>

            <!-- Currency -->
            <div class="mb-3">
                <label for="currency" class="form-label">Currency</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                    <option value="BDT">BDT - Bangladeshi Taka</option>
                    <option value="JPY">JPY - Japanese Yen</option>
                    <option value="AUD">AUD - Australian Dollar</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Create Bank Account</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('script')

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable();
    });
@endsection