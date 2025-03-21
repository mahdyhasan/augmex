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
                    <table class="table table-bordered">
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
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <input type="text" class="form-control" value="{{ $item->employee_name }}" readonly>
                                </td>
                                <td><input type="number" class="form-control" name="items[{{ $index }}][days_worked]" value="{{ $item->days_worked }}" required></td>
                                <td><input type="number" class="form-control" name="items[{{ $index }}][hours_worked]" value="{{ $item->hours_worked }}" required></td>
                                <td><input type="number" class="form-control rate-field" name="items[{{ $index }}][rate]" value="{{ $item->rate }}" step="0.01" required></td>
                                <td><input type="number" class="form-control deduction-field" name="items[{{ $index }}][deductions]" value="{{ $item->deductions }}" step="0.01"></td>
                                <td><input type="number" class="form-control commission-field" name="items[{{ $index }}][commission]" value="{{ $item->commission }}" step="0.01"></td>
                                <td><input type="number" class="form-control amount-field" name="items[{{ $index }}][amount]" value="{{ $item->amount }}" step="0.01" required></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Total Amount -->
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount ({{ $currency }})</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" value="{{ $invoice->total_amount }}" step="0.01" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
    // Auto-calculate total amount on field change
    document.querySelectorAll('.amount-field, .rate-field, .deduction-field, .commission-field').forEach(field => {
        field.addEventListener('input', updateTotal);
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-field').forEach(field => {
            total += parseFloat(field.value) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }
</script>
@endsection
