@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Invoice</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Invoice Date -->
                    <div class="mb-3">
                        <label for="invoice_date" class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ $invoice->invoice_date }}" required>
                    </div>

                    <!-- Client Selection -->
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select class="form-control" id="client_id" name="client_id" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Currency (Fetched from ClientConditions) -->
                    @php
                        $currency = $invoice->client->clientConditions->first()->currency ?? 'N/A';
                    @endphp
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <input type="text" class="form-control" id="currency" name="currency" value="{{ $currency }}" readonly>
                    </div>

                    <!-- Invoice Items -->
                    <h5 class="mt-4">Invoice Items</h5>
                    <table class="table table-bordered" id="invoiceItemsTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Days</th>
                                <th>Hours</th>
                                <th>Rate ({{ $currency }})</th>
                                <th>Deductions ({{ $currency }})</th>
                                <th>Commission ({{ $currency }})</th>
                                <th>Amount ({{ $currency }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->invoiceItems as $index => $item)
                            <tr class="invoice-item-row">
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <input type="text" class="form-control" value="{{ $item->employee_name }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control days-field" 
                                           name="items[{{ $index }}][days_worked]" 
                                           value="{{ $item->days_worked }}" 
                                           min="0" step="1" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control hours-field" 
                                           name="items[{{ $index }}][hours_worked]" 
                                           value="{{ $item->hours_worked }}" 
                                           min="0" step="0.01" required readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control rate-field" 
                                           name="items[{{ $index }}][rate]" 
                                           value="{{ $item->rate }}" 
                                           min="0" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control deduction-field" 
                                           name="items[{{ $index }}][deductions]" 
                                           value="{{ $item->deductions }}" 
                                           min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" class="form-control commission-field" 
                                           name="items[{{ $index }}][commission]" 
                                           value="{{ $item->commission }}" 
                                           min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" class="form-control amount-field" 
                                           name="items[{{ $index }}][amount]" 
                                           value="{{ $item->amount }}" 
                                           min="0" step="0.01" required readonly>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Total Amount -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Invoice Total</h5>
                                        <h4 class="mb-0">
                                            <span id="totalAmountDisplay">{{ number_format($invoice->total_amount, 2) }}</span> 
                                            {{ $currency }}
                                        </h4>
                                    </div>
                                    <input type="hidden" id="total_amount" name="total_amount" value="{{ $invoice->total_amount }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all relevant input fields
    const invoiceTable = document.getElementById('invoiceItemsTable');
    
    // Add event listeners to all editable fields
    invoiceTable.addEventListener('input', function(e) {
        if (e.target.classList.contains('days-field') || 
            e.target.classList.contains('rate-field') || 
            e.target.classList.contains('deduction-field') || 
            e.target.classList.contains('commission-field')) {
            
            const row = e.target.closest('.invoice-item-row');
            calculateRowAmount(row);
            calculateTotalAmount();
        }
    });
    
    // Initial calculations
    document.querySelectorAll('.invoice-item-row').forEach(row => {
        calculateRowAmount(row);
    });
    calculateTotalAmount();
    
    function calculateRowAmount(row) {
        const days = parseFloat(row.querySelector('.days-field').value) || 0;
        const rate = parseFloat(row.querySelector('.rate-field').value) || 0;
        const deductions = parseFloat(row.querySelector('.deduction-field').value) || 0;
        const commission = parseFloat(row.querySelector('.commission-field').value) || 0;
        
        // Calculate hours (days * 8)
        const hours = days * 8;
        row.querySelector('.hours-field').value = hours.toFixed(2);
        
        // Calculate amount ((hours * rate) - deductions + commission)
        const amount = (hours * rate) - deductions + commission;
        row.querySelector('.amount-field').value = amount.toFixed(2);
    }
    
    function calculateTotalAmount() {
        let total = 0;
        document.querySelectorAll('.amount-field').forEach(field => {
            total += parseFloat(field.value) || 0;
        });
        
        document.getElementById('totalAmountDisplay').textContent = total.toFixed(2);
        document.getElementById('total_amount').value = total.toFixed(2);
    }
});
</script>
@endsection