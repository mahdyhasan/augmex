<!DOCTYPE html>
<html>
<head>
    <title>Salary Sheet - {{ $month->format('F Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 15px; }
        .period { font-size: 12pt; margin-bottom: 5px; }
        .summary { margin-top: 15px; font-weight: bold; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Salary Sheet</h2>
        <div class="period">
            Period: {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">SL</th>
                <th width="15%">Employee Name</th>
                <th width="12%">Department</th>
                <th width="12%">Position</th>
                <th width="10%">Base Salary</th>
                <th width="8%">Bonuses</th>
                <th width="8%">Commission</th>
                <th width="8%">Transport</th>
                <th width="8%">Others</th>
                <th width="8%">Deductions</th>
                <th width="10%">Net Salary</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $index => $payroll)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payroll->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $payroll->employee->department ?? '-' }}</td>
                <td>{{ $payroll->employee->position ?? '-' }}</td>
                <td class="text-right">{{ number_format($payroll->base_salary, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->bonuses, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->commission, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->transport, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->others, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->deductions, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->net_salary, 2) }}</td>
                <td>{{ ucfirst($payroll->payment_status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>Total Employees: {{ count($payrolls) }}</p>
        <p>Generated On: {{ now()->format('d M Y h:i A') }}</p>
    </div>
</body>
</html>