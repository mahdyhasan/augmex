<div class="payslip-container" style="max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #eee; font-family: Arial, sans-serif;">
    <!-- Company Header -->
    <div class="text-center mb-4" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px;">
        <h1 style="margin: 0; color: #2c3e50;">{{ config('app.name') }}</h1>
        <p style="margin: 5px 0 0; color: #7f8c8d;">Salary Payslip</p>
    </div>

    <!-- Employee & Pay Period Info -->
    <div class="d-flex justify-content-between mb-4" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="employee-info" style="flex: 1;">
            <div class="d-flex align-items-center" style="display: flex; align-items: center;">
                <div class="avatar me-3" style="margin-right: 15px;">
                    <img src="{{ $payroll->employee->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                         style="border-radius: 50%; width: 60px; height: 60px; border: 1px solid #ddd;">
                </div>
                <div>
                    <h3 style="margin: 0 0 5px; color: #2c3e50;">{{ $payroll->employee->user->name }}</h3>
                    <p style="margin: 0; color: #7f8c8d;">{{ $payroll->employee->position ?? 'N/A' }}</p>
                    <p style="margin: 5px 0 0; color: #7f8c8d;">Employee ID: {{ $payroll->employee->employee_id }}</p>
                </div>
            </div>
        </div>
        
        <div class="pay-period" style="flex: 1; text-align: right;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 3px 0; text-align: right;"><strong>Pay Period:</strong></td>
                    <td style="padding: 3px 0; text-align: right;">{{ $payroll->pay_period_start->format('d M Y') }} - {{ $payroll->pay_period_end->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px 0; text-align: right;"><strong>Payment Date:</strong></td>
                    <td style="padding: 3px 0; text-align: right;">{{ $payroll->payment_date ? $payroll->payment_date->format('d M Y') : 'Pending' }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px 0; text-align: right;"><strong>Status:</strong></td>
                    <td style="padding: 3px 0; text-align: right;">
                        <span style="background-color: {{ $payroll->payment_status == 'paid' ? '#2ecc71' : '#f39c12' }}; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                            {{ ucfirst($payroll->payment_status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Salary Breakdown -->
    <div class="salary-breakdown mb-4" style="margin-bottom: 30px;">
        <h4 style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #3498db; margin: 0 0 15px;">Salary Breakdown</h4>
        
        <div class="row" style="display: flex; margin: 0 -10px;">
            <div class="col-md-6" style="flex: 1; padding: 0 10px;">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Base Salary</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ number_format($payroll->base_salary, 2) }} BDT</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Bonuses</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ number_format($payroll->bonuses, 2) }} BDT</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Commission</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ number_format($payroll->commission, 2) }} BDT</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6" style="flex: 1; padding: 0 10px;">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Transport Allowance</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ number_format($payroll->transport, 2) }} BDT</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Other Allowances</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ number_format($payroll->others, 2) }} BDT</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Deductions</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">-{{ number_format($payroll->deductions, 2) }} BDT</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Net Salary -->
    <div class="net-salary" style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: #2c3e50;">Net Salary</h4>
            <h3 style="margin: 0; color: #27ae60;">{{ number_format($payroll->net_salary, 2) }} BDT</h3>
        </div>
    </div>

    <!-- Footer -->
    <div class="payslip-footer" style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 20px; font-size: 12px; color: #7f8c8d;">
        <div class="row" style="display: flex; margin: 0 -10px;">
            <div class="col-md-6" style="flex: 1; padding: 0 10px;">
                <p style="margin: 5px 0;"><strong>Generated On:</strong> {{ now()->format('d M Y h:i A') }}</p>
            </div>
            <div class="col-md-6 text-right" style="flex: 1; padding: 0 10px; text-align: right;">
                <p style="margin: 5px 0;">This is a computer generated payslip. No signature required.</p>
            </div>
        </div>
    </div>

    <!-- Print Button (visible only on screen) -->
    <div class="text-center mt-3 no-print" style="text-align: center; margin-top: 20px; @media print { display: none; }">
        <button onclick="window.print()" class="btn btn-primary" style="background-color: #3498db; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Print Payslip
        </button>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .payslip-container {
            border: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
    }
</style>