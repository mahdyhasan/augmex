@extends('layouts.app')

@section('title', 'Bank Accounts')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Bank Accounts</h3>
                    <a href="{{ route('bank_accounts.create') }}" class="btn btn-primary">Add Bank Account</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="bankTable table-bordered">
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
@endsection

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#bankTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true
        });
    });
</script>
@endsection