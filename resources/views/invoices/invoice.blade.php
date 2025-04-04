@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_no)

@section('content')
<div class="bg-white p-4 rounded shadow-sm" style="max-width: 900px; margin: auto;">
    <!-- Logo + Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <img src="{{ asset('public/assets/img/profiles/tcl.png') }}" alt="Logo" style="height: 60px;">
        <div class="text-end">
            <h2 class="fw-bold mb-1">INVOICE</h2>
            <p class="mb-0">Invoice No: <strong>{{ $invoice->invoice_no }}</strong></p>
            <p class="mb-0">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
            <p class="mb-0">Work Date: {{ \Carbon\Carbon::parse($invoice->work_start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($invoice->work_end_date)->format('d M Y') }}</p>
        </div>
    </div>

    <!-- To/From -->
    <div class="row mb-4">
        <div class="col-md-6">
        <h5 class="fw-bold">Invoice To:</h5>
        <p>
            {{ $invoice->client->agency ?? $invoice->client->company }}<br>
            {{ $invoice->client->kdm }}<br>
            {{ $invoice->client->address ?? '' }}
        </p>
        </div>
        <div class="col-md-6 text-end">
            <h5 class="fw-bold">From:</h5>
            <p>
                Tech Cloud Ltd.<br>
                H#379, R#06, Baridhara DOHS,<br>
                Dhaka-1206, Bangladesh<br>
                Phone: +880-1711-708-848<br>
                mahdy@techcloudltd.com
            </p>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-bordered text-center">
        <thead class="table-light">
            <tr>
                <th>Employee</th>
                <th>Days</th>
                <th>Hours</th>
                <th>Rate ({{ $currency }})</th>
                <th>Deductions</th>
                <th>Commission</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Initialize totals
                $totalHours = 0;
                $totalDeductions = 0;
                $totalCommission = 0;
                $totalAmount = 0;
            @endphp
            
            @foreach($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->employee_name }}</td>
                    <td>{{ $item->days_worked }}</td>
                    <td>{{ number_format($item->hours_worked, 2) }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->deductions, 2) }}</td>
                    <td>{{ number_format($item->commission, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
                @php
                    // Accumulate totals
                    $totalHours += $item->hours_worked;
                    $totalDeductions += $item->deductions;
                    $totalCommission += $item->commission;
                    $totalAmount += $item->amount;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <!-- Column Totals Row -->
            <tr class="fw-bold">
                <td class="text-end">Subtotals:</td>
                <td></td>
                <td>{{ number_format($totalHours, 2) }}</td>
                <td></td>
                <td>{{ number_format($totalDeductions, 2) }}</td>
                <td>{{ number_format($totalCommission, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
            
            <!-- Grand Total Row -->
            <tr class="fw-bold table-active">
                <td colspan="6" class="text-end">GRAND TOTAL ({{ $currency }}):</td>
                <td>{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- In words -->
    <!-- @if($invoice->amount_in_words)
    <p><strong>In Words:</strong> {{ ucfirst($invoice->amount_in_words) }} only</p>
    @endif -->

    <!-- Bank Info -->
    @php
        $bank = \App\Models\BankAccount::first(); // Or use a preferred bank
    @endphp
    <div class="mt-4">
        <h5 class="fw-bold">Bank Details</h5>
        <p>
            Bank Name: {{ $bank->bank_name }}<br>
            Branch: {{ $bank->branch }}<br>
            Account Number: {{ $bank->account_number }}<br>
            Bank Address: {{ $bank->address }}<br>
            Beneficiary Name: {{ $bank->name }}
        </p>
    </div>

    <!-- Print -->
    <div class="text-center mt-4 d-print-none">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
    </div>
</div>
@endsection

@section ('css')

<style>
    @media print {
        /* Hide header and navigation */
        header, .navbar, .breadcrumb, .d-print-none, .menu-horizontal .header, .sidebar-contact .toggle-theme {
            display: none !important;
        }
        
        /* Show only the invoice card */
        .card {
            border: none;
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
        
        /* Ensure proper spacing for printed content */
        body {
            padding: 0 20px !important;
            /*margin-top: 0px !important;*/
            font-size: 12pt;
        }
        
        /* Keep footer visible (if you have one) */
        footer {
            display: block !important;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    }
</style>


@endsection


