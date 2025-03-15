@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Expenses</h3>
                    <a href="javascript:void(0);" class="btn btn-primary"
                                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i
                                            class="ti ti-square-rounded-plus me-2"></i>Add Expense</a>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" action="{{ route('expenses.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary mt-4">Filter</button>
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
                                    <td>{{ $expense->expense_date }}</td>
                                    <td>{{ $expense->expenseCategory->name }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ number_format($expense->amount) }}</td>
                                    <td>
                                        @if($expense->receipt)
                                            <a href="{{ asset('public/assets/receipts/' . $expense->receipt) }}" target="_blank">View</a>
                                        @else
                                            N/A
                                        @endif
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
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
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

@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#expenseTable').DataTable();
    });
</script>
@endsection
