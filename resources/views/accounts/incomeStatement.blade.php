@extends('layouts.app')

@section('title', 'Income Statement')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Income Statement</h4>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Statement
                    </button>
                </div>

                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" action="{{ route('accounts.incomeStatement') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Income Statement -->
                    <div class="financial-statement">
                        <!-- Header -->
                        <div class="statement-header text-center mb-4">
                            <h3>Income Statement</h3>
                            <h5 class="text-muted">{{ $startDate }} to {{ $endDate }}</h5>
                        </div>
                        
                        <!-- Revenue Section -->
                        <div class="statement-section mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Revenue</h5>
                                <span class="badge bg-light text-dark">Total: {{ number_format($revenueBdt, 2) }} BDT</span>
                            </div>
                            
                            <div class="revenue-details">
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span>Original Currency Revenue</span>
                                    <span>{{ number_format($revenueOriginal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span>Exchange Rate Applied (BDT)</span>
                                    <span>
                                        @foreach($exchangeRates as $currency => $rate)
                                            {{ $currency }}: {{ $rate }}@if(!$loop->last), @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenses Section -->
                        <div class="statement-section mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Expenses</h5>
                                <span class="badge bg-light text-dark">Total: {{ number_format($totalExpenses, 2) }} BDT</span>
                            </div>
                            
                            <div class="expenses-list">
                                @foreach($expensesByCategory as $expense)
                                    @if($expense['amount'] > 0)
                                    <div class="expense-category mb-2">
                                        <a href="#" class="d-flex justify-content-between align-items-center py-2 px-3 rounded expense-category-link" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#expenseModal"
                                           onclick="showExpenseDetails(
                                               '{{ $expense['name'] }}',
                                               '{{ $startDate }}',
                                               '{{ $endDate }}',
                                               {{ json_encode($expense['expense_details']) }}
                                           )">
                                            <span>{{ $expense['name'] }}</span>
                                            <span class="badge bg-light text-dark">{{ number_format($expense['amount'], 2) }}</span>
                                        </a>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Net Income Section -->
                        <div class="statement-section p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Net Income</h5>
                                <h5 class="{{ $netIncome >= 0 ? 'text-success' : 'text-dark' }}">
                                    {{ number_format($netIncome, 2) }} BDT
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expense Details Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalTitle">Expense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <span id="expensePeriod"></span>
                    <strong id="expenseTotal"></strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="expenseDetailsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .financial-statement {
        max-width: 800px;
        margin: 0 auto;
    }
    .statement-section {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .expense-category-link {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #212529;
    }
    .expense-category-link:hover {
        background-color: #e9ecef;
    }
    @media print {
        .card-header, .modal {
            display: none !important;
        }
        body {
            background: white;
            color: black;
        }
        .financial-statement {
            max-width: 100%;
        }
    }
</style>
@endsection

@section('js')
<script>

document.addEventListener('DOMContentLoaded', function() {
    // Get the modal element
    const expenseModal = document.getElementById('expenseModal');
    
    if (expenseModal) {
        // Initialize the modal
        const modal = new bootstrap.Modal(expenseModal);
        
        // Clear modal content when hidden
        expenseModal.addEventListener('hidden.bs.modal', function() {
            // Remove the modal backdrop if it exists
            const backdrops = document.getElementsByClassName('modal-backdrop');
            while (backdrops.length > 0) {
                backdrops[0].parentNode.removeChild(backdrops[0]);
            }
            
            // Remove the modal-open class from body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = ''; // Reset overflow
            document.body.style.paddingRight = ''; // Reset padding
        });
    }

    // Your existing showExpenseDetails function
    function showExpenseDetails(categoryName, startDate, endDate, expenseDetails) {
        // Set modal title and period
        document.getElementById('expenseModalTitle').textContent = `${categoryName} Expenses`;
        document.getElementById('expensePeriod').textContent = `Period: ${startDate} to ${endDate}`;
        
        // Calculate total
        const total = expenseDetails.reduce((sum, expense) => sum + parseFloat(expense.amount), 0);
        document.getElementById('expenseTotal').textContent = `Total: ${total.toFixed(2)}`;
        
        // Populate table
        const tbody = document.querySelector('#expenseDetailsTable tbody');
        tbody.innerHTML = '';
        
        if (expenseDetails.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No expenses found</td></tr>';
            return;
        }
        
        expenseDetails.forEach(expense => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${new Date(expense.date).toLocaleDateString()}</td>
                <td>${expense.description || '-'}</td>
                <td>${parseFloat(expense.amount).toFixed(2)}</td>
                <td>
                    ${expense.receipt ? 
                        `<a href="/storage/${expense.receipt}" target="_blank">View</a>` : 
                        'None'}
                </td>
            `;
            tbody.appendChild(row);
        });
        
        // Show modal using Bootstrap
        const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
        modal.show();
    }
    
    // Make the function available globally
    window.showExpenseDetails = showExpenseDetails;
});

</script>
@endsection