<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .company-info, .client-info {
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        .bank-details {
            margin-top: 20px;
        }
        .print-button {
            margin-top: 20px;
            text-align: center;
        }
        .print-button button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <h2>INVOICE</h2>
        <h4>Invoice No: {{ $invoice->id }}</h4>
        <h5>Invoice Date: {{ $invoice->invoice_date }}</h5>
        <h5>Work Date: {{ $invoice->work_start_date }} - {{ $invoice->work_end_date }}</h5>
    </div>

    <div class="company-info">
        <div>
            <h5>Invoice To:</h5>
            <p>{{ $invoice->client->company }}<br>{{ $invoice->client->country }}</p>
        </div>
        <div>
            <h5>From:</h5>
            <p>Tech Cloud Ltd.<br>H#379, R#06, Baridhara DOHS, Dhaka-1206, Bangladesh</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Days</th>
                <th>Hours</th>
                <th>Rate</th>
                <th>Deductions</th>
                <th>Commission</th>
                <th>Amount (Tk)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->employee_name }}</td>
                    <td>{{ $item->days_worked }}</td>
                    <td>{{ $item->hours_worked }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->deductions, 2) }}</td>
                    <td>{{ number_format($item->commission, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="total">Total:</th>
                <th>{{ number_format($invoice->total_amount, 2) }} Tk</th>
            </tr>
        </tfoot>
    </table>

    <p class="mt-4"><strong>In Words:</strong> {{ ucfirst($invoice->amount_in_words) }} Taka only</p>

    <div class="bank-details">
        <h5>Bank Details</h5>
        <p>
            Bank Name: Citibank<br>
            Bank Address: 2 Park Street, Sydney NSW 2000<br>
            Branch Code (BSB): 248024<br>
            Account Number: 10009596<br>
            Beneficiary: Tech Cloud Ltd
        </p>
    </div>

    <div class="print-button">
        <button onclick="window.print()">Print Invoice</button>
    </div>
</div>

</body>
</html>
