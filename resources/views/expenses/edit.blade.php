@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Expense</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Expense Date -->
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">Expense Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="{{ $expense->expense_date }}" required>
                    </div>

                    <!-- Expense Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $expense->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ $expense->description }}">
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (Tk)</label>
                        <input type="number" class="form-control" id="amount" name="amount" value="{{ $expense->amount }}" required>
                    </div>

                    <!-- Receipt Upload -->
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Receipt (Optional)</label>
                        <input type="file" class="form-control" id="receipt" name="receipt">
                        @if($expense->receipt)
                            <p class="mt-2"><a href="{{ asset('public/assets/receipts/' . $expense->receipt) }}" target="_blank">View Current Receipt</a></p>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <button type="submit" class="btn btn-primary">Update Expense</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
