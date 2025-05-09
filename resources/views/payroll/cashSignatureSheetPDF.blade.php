<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Signature Sheet - {{ $monthName }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 120px 30px;
            background: #fff;
            color: #333;
        }
        .title {
            text-align: center;
            font-weight: 700;
            font-size: 22px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid #000;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
            font-size: 14px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }
        td.text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: 700;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 100px;
        }
        .footer table {
            width: 100%;
            border: none;
            text-align: center;
        }
        .footer th, .footer td {
            border: none;
            font-size: 14px;
            padding: 5px;
        }
        .footer .label {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 15px;
            text-decoration: underline;
        }
        .footer .info {
            font-size: 14px;
            margin-top: 5px;
            color: #333;
        }
        @media print {
            body {
                background: #fff;
            }
            table, .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="title">Cash Salary for the Month of {{ $monthName }}</div>

    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>ID</th>
                <th>Name</th>
                <th>BDT</th>
                <th>Signature & Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $index => $payroll)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payroll->employee->id ?? 'N/A' }}</td>
                <td>{{ $payroll->employee->user->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($payroll->net_salary, 2) }}</td>
                <td></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">{{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <table>
            <thead>
                <tr>
                    <th>Prepared By</th>
                    <th>Checked By</th>
                    <th>Approved By</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>S.M.Syful Islam</td>
                    <td>Mahdy Hasan</td>
                    <td>SJ Tarique</td>
                </tr>
                <tr>
                    <td style="color: grey;">Consultant</td>
                    <td style="color: grey;">Head of Growth</td>
                    <td style="color: grey;">Managing Director</td>
                </tr>                <tr>
                    <td style="color: grey;">Tech Cloud Ltd.</td>
                    <td style="color: grey;">Tech Cloud Ltd.</td>
                    <td style="color: grey;">Tech Cloud Ltd.</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
