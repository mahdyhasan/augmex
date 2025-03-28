@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Expenses</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_expense">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Expense
                </a>
            </div>
          <div class="card-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Explanation for users -->
    <div class="alert alert-secondary">
        <h5>How to Use Expenses?</h5>
        <p>
            Expenses track company spending on goods/services.  
            - View past expenses including rent, travel, and office costs.  
            - Add a new expense using the "Add Expense" button.  
            - Upload receipts for better financial tracking.  
            - Expenses reduce cash balance but do not track incoming money.  
            - If you need to record money received (income), use the Transactions page instead.
        </p>
    </div>

            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('expenses.index') }}" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="{{ request('start_date', $startDate) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="{{ request('end_date', $endDate) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('expenses.index') }}" class="btn btn-dark w-100">Reset</a>
                    </div>
                    
                </div>
            </form>

            <!-- Expense Table -->
            <table id="expenseTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount (Tk)</th>
                        <th>Receipt</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                        <tr>
                            <td>{{ $expense->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</td>
                            <td>{{ $expense->expenseCategory->name ?? 'N/A' }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ number_format($expense->amount) }}</td>
                            <td>
                                @if($expense->receipt)
                                    <a href="{{ asset('public/assets/receipts/' . $expense->receipt) }}" target="_blank">View</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">Delete</button>
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



    <!-- Offcanvas: Add Expense -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_expense">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Record an Expense</h5> 
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>Expense Date</label>
                    <input type="date" name="expense_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Receipt (Optional)</label>
                    <input type="file" name="receipt" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Record</button>
            </form>
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
        $('#expenseTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true
        });
    });
</script>
@endsection