<!DOCTYPE html>
<html>
<head>
    <title>Cash Signature Sheet - {{ $monthName }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin-top: 200px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        td, th { border: 1px solid #000; padding: 8px; text-align: left; }
        .title { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .footer { margin-top: 30px; }
        .footer table { width: 100%; border: none; }
        .footer td { border: none; padding: 3px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="title">Cash Salary for the Month of {{ $monthName }}.</div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="15%">ID</th>
                <th width="35%">Name</th>
                <th width="20%">BDT</th>
                <th width="25%">Signature & Date</th>
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
            <tr>
                <td colspan="3" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($total, 2) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <br>    <br>


    <div class="footer" style="margin-top: 50px;">
        <table>
            <tr>
                <td width="33%">Prepared By</td>
                <td width="33%">Checked By</td>
                <td width="33%">Approved By</td>
            </tr>
            <tr>
                <td>S.M.Syful Islam</td>
                <td>Mahdy Hasan</td>
                <td>SJ Tarique</td>
            </tr>
            <tr>
                <td>Consultant</td>
                <td>Head of Growth</td>
                <td>Managing Director</td>
            </tr>
            <tr>
                <td>Tech Cloud Ltd.</td>
                <td>Tech Cloud Ltd.</td>
                <td>Tech Cloud Ltd.</td>
            </tr>
        </table>
    </div>
</body>
</html>