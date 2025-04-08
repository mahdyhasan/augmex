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

                    <!-- Invoice Basic Information -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ $invoice->invoice_date }}" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="work_date" class="form-label">Work Period</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="work_start_date" value="{{ $invoice->work_start_date }}" required>
                                    <span class="input-group-text">to</span>
                                    <input type="date" class="form-control" name="work_end_date" value="{{ $invoice->work_end_date }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="invoice_no" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="{{ $invoice->invoice_no }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency" value="{{ $invoice->client->clientConditions->first()->currency ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <h5 class="mt-4">Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoiceItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Days</th>
                                    <th>Hours</th>
                                    <th>Rate </th>
                                    <th>Deductions</th>
                                    <th>Commission </th>
                                    <th>Amount </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->invoiceItems as $index => $item)
                                <tr class="invoice-item-row">
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <input type="text" class="form-control employee-name" 
                                               name="items[{{ $index }}][employee_name]" 
                                               value="{{ $item->employee_name }}" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control days-field" 
                                               name="items[{{ $index }}][days_worked]" 
                                               value="{{ $item->days_worked }}" 
                                               min="0" step="0.5" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control hours-field" 
                                               name="items[{{ $index }}][hours_worked]" 
                                               value="{{ $item->hours_worked }}" 
                                               min="0" step="0.01" required>
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
                                               min="0" step="0.01" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--<button type="button" class="btn btn-success btn-sm" id="add-row"><i class="fas fa-plus"></i> Add Row</button>-->
                    </div>

                    <!-- Totals Section -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">Subtotal:</h5>
                                        <h5 class="mb-0">
                                            <span id="subtotalAmount">{{ number_format($invoice->total_amount, 2) }}</span> 
                                            
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">Tax (0%):</h5>
                                        <h5 class="mb-0">
                                            <span id="taxAmount">0.00</span> 
                                            
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">Total Amount:</h4>
                                        <h4 class="mb-0">
                                            <span id="totalAmountDisplay">{{ number_format($invoice->total_amount, 2) }}</span> 
                                            
                                        </h4>
                                    </div>
                                    <input type="hidden" id="total_amount" name="total_amount" value="{{ $invoice->total_amount }}">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
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
    const currency = "{{ $invoice->client->clientConditions->first()->currency ?? '' }}";
    const invoiceTable = document.getElementById('invoiceItemsTable');
    let rowCount = {{ count($invoice->invoiceItems) }};
    
    // Add new row
    // document.getElementById('add-row').addEventListener('click', function() {
    //     const newRow = document.createElement('tr');
    //     newRow.className = 'invoice-item-row';
    //     newRow.innerHTML = `
    //         <td>
    //             <input type="hidden" name="items[${rowCount}][id]" value="">
    //             <input type="text" class="form-control employee-name" name="items[${rowCount}][employee_name]" required>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control days-field" name="items[${rowCount}][days_worked]" 
    //                   min="0" step="0.5" required>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control hours-field" name="items[${rowCount}][hours_worked]" 
    //                   min="0" step="0.01" required>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control rate-field" name="items[${rowCount}][rate]" 
    //                   min="0" step="0.01" required>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control deduction-field" name="items[${rowCount}][deductions]" 
    //                   min="0" step="0.01">
    //         </td>
    //         <td>
    //             <input type="number" class="form-control commission-field" name="items[${rowCount}][commission]" 
    //                   min="0" step="0.01">
    //         </td>
    //         <td>
    //             <input type="number" class="form-control amount-field" name="items[${rowCount}][amount]" 
    //                   min="0" step="0.01" readonly>
    //         </td>
    //         <td>
    //             <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
    //         </td>
    //     `;
    //     invoiceTable.querySelector('tbody').appendChild(newRow);
    //     rowCount++;
    // });
    
    // Remove row
    invoiceTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('.invoice-item-row');
            row.remove();
            calculateTotalAmount();
        }
    });
    
    // Calculate amounts when fields change
    invoiceTable.addEventListener('input', function(e) {
        if (e.target.classList.contains('days-field') || 
            e.target.classList.contains('hours-field') || 
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
    
    // Calculate row amount
    function calculateRowAmount(row) {
        const days = parseFloat(row.querySelector('.days-field').value) || 0;
        const hours = parseFloat(row.querySelector('.hours-field').value) || 0;
        const rate = parseFloat(row.querySelector('.rate-field').value) || 0;
        const deductions = parseFloat(row.querySelector('.deduction-field').value) || 0;
        const commission = parseFloat(row.querySelector('.commission-field').value) || 0;
        
        // If hours field is empty but days has value, calculate hours (days * 8)
        if (hours === 0 && days > 0) {
            const calculatedHours = days * 8;
            row.querySelector('.hours-field').value = calculatedHours.toFixed(2);
        }
        
        // Calculate amount ((hours * rate) - deductions + commission)
        const currentHours = parseFloat(row.querySelector('.hours-field').value) || 0;
        const amount = (currentHours * rate) - deductions + commission;
        row.querySelector('.amount-field').value = amount.toFixed(2);
    }
    
    // Calculate total amount
    function calculateTotalAmount() {
        let subtotal = 0;
        document.querySelectorAll('.amount-field').forEach(field => {
            subtotal += parseFloat(field.value) || 0;
        });
        
        const tax = 0; // You can add tax calculation here if needed
        const total = subtotal + tax;
        
        document.getElementById('subtotalAmount').textContent = subtotal.toFixed(2);
        document.getElementById('taxAmount').textContent = tax.toFixed(2);
        document.getElementById('totalAmountDisplay').textContent = total.toFixed(2);
        document.getElementById('total_amount').value = total.toFixed(2);
    }
    
    // Update currency when client changes
    document.getElementById('client_id').addEventListener('change', function() {
        const clientId = this.value;
        // You would need to fetch the currency via AJAX here
        // This is just a placeholder - implement your actual currency fetch logic
        console.log('Client changed to ID:', clientId);
    });
});
</script>
@endsection

@section('css')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .invoice-item-row td {
        vertical-align: middle;
    }
    .form-control[readonly] {
        background-color: #f8f9fa;
    }
    .table th {
        white-space: nowrap;
    }
    #add-row {
        margin-top: 10px;
    }
    .remove-row {
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection