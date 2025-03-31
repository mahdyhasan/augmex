<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

use DataTables;
use Excel;
use PDF; 

use App\Models\Account;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\ClientPayment;
use App\Models\DivanjCommission;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\EmployeeSales;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

use App\Models\User;


class PayrollController extends Controller
{

    public function __construct()
    {
        $this->middleware('superadmin');
    }
    
    
    public function index()
    {
        $payrolls = Payroll::with('employee', 'employee.user')->orderBy('pay_period_start', 'desc')->get();
        $employees = Employee::with('user')->get(); // Fetch employees for the dropdown
        return view('payroll.index', compact('payrolls', 'employees'));
    }


    public function edit($id)
    {
        $payroll = Payroll::with(['employee.user'])->findOrFail($id);
        return view('payroll.edit', compact('payroll'));
    }

    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'transport' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|date'
        ]);

        // Calculate net salary
        $validated['net_salary'] = $validated['base_salary'] 
                                + ($validated['bonuses'] ?? 0)
                                + ($validated['commission'] ?? 0)
                                + ($validated['transport'] ?? 0)
                                + ($validated['others'] ?? 0)
                                - ($validated['deductions'] ?? 0);

        $payroll->update($validated);

        return redirect()->route('payrolls.index')
                    ->with('success', 'Payroll updated successfully');
    }

    public function view($id)
    {
        $payroll = Payroll::with(['employee.user', 'employee'])
                        ->findOrFail($id);
        
        return view('payroll.view', compact('payroll'));
    }


    public function markAsPaid(Request $request, $id)
{
    try {
        $payroll = Payroll::findOrFail($id);

        $payroll->payment_status = 'paid';
        $payroll->payment_date = now(); // Set payment date to today's date
        $payroll->save();

        return redirect()->route('payrolls.index')->with('success', 'Mark as paid successfully.');
    } catch (\Exception $e) {
        return redirect()->route('payrolls.index')->with('error', 'Error: ' . $e->getMessage());
    }
}


public function generateAll(Request $request)
{
    try {
        // First validate the input as a string
        $validated = $request->validate([
            'month' => 'required|string|date_format:Y-m'
        ]);
        
        // Then parse the validated string
        $selectedMonth = Carbon::parse($validated['month']);

        // Currency conversion rates
        $currencyRates = [
            'AUD' => 75,
            'EUR' => 130, 
            'GBP' => 145,
            'USD' => 120
        ];

        $startDate = $selectedMonth->copy()->subMonth()->day(25);
        $endDate = $selectedMonth->copy()->day(24);

        $employees = Employee::with('user')->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No employees found'
            ], 200);
        }

        $generatedCount = 0;
        $updatedCount = 0;
        $errors = [];

        foreach ($employees as $employee) {
            try {
                if (empty($employee->salary_amount)) {
                    $errors[] = "Skipped: Employee {$employee->user->name} has no salary amount set";
                    continue;
                }

                // 1. Calculate total working days (weekdays only, Monday-Friday)
                $totalWorkingDays = 0;
                $currentDate = $startDate->copy();
                
                while ($currentDate <= $endDate) {
                    if ($currentDate->isWeekday()) {
                        $totalWorkingDays++;
                    }
                    $currentDate->addDay();
                }

                // 2. Calculate attendance (only weekdays with check_in)
                $attendanceCount = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotNull('check_in')
                    ->whereRaw('WEEKDAY(date) < 5')
                    ->count();

                // 3. Calculate deductions (only for weekday absences)
                $absentDays = max(0, $totalWorkingDays - $attendanceCount);
                
                // Calculate per day salary considering only working days
                $perDaySalary = $totalWorkingDays > 0 ? $employee->salary_amount / $totalWorkingDays : 0;

                $deduction = $absentDays * $perDaySalary;

                // 4. Calculate commission in BDT
                $totalCommission = 0;
                
                $commissions = DivanjCommission::where('employee_id', $employee->id)
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->get();

                foreach ($commissions as $commission) {
                    $clientCondition = ClientCondition::where('client_id', $employee->client_id)
                        ->first();
                    
                    $currency = $clientCondition->currency ?? 'AUD';
                    $rate = $currencyRates[strtoupper($currency)] ?? 75;
                    
                    $totalCommission += $commission->commission_amount * $rate;
                }

                // 5. Calculate net salary
                $netSalary = $employee->salary_amount + $totalCommission - $deduction;

                // 6. Update or create payroll record
                $payrollData = [
                    'year' => $selectedMonth->year,
                    'month' => $selectedMonth->month,
                    'employee_id' => $employee->id,
                    'base_salary' => $employee->salary_amount,
                    'commission' => $totalCommission,
                    'deductions' => $deduction,
                    'net_salary' => $netSalary,
                    'payment_status' => 'pending', // Default status
                    'pay_period_start' => $startDate,
                    'pay_period_end' => $endDate
                ];

                // Only update these fields if they exist in the original record
                if ($existing = Payroll::where('employee_id', $employee->id)
                    ->where('pay_period_start', $startDate)
                    ->where('pay_period_end', $endDate)
                    ->first()) {
                    $payrollData['bonuses'] = $existing->bonuses ?? 0;
                    $payrollData['transport'] = $existing->transport ?? 0;
                    $payrollData['others'] = $existing->others ?? 0;
                    $payrollData['payment_status'] = $existing->payment_status;
                    $payrollData['payment_date'] = $existing->payment_date;
                    $updatedCount++;
                } else {
                    $payrollData['bonuses'] = 0;
                    $payrollData['transport'] = 0;
                    $payrollData['others'] = 0;
                    $generatedCount++;
                }

                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'pay_period_start' => $startDate,
                        'pay_period_end' => $endDate
                    ],
                    $payrollData
                );

            } catch (\Exception $e) {
                $errors[] = "Error processing {$employee->user->name}: " . $e->getMessage();
                continue;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Payroll processed successfully",
            'details' => [
                'generated' => $generatedCount,
                'updated' => $updatedCount,
                'errors' => count($errors),
                'total_employees' => count($employees)
            ],
            'warnings' => $errors
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'System error: ' . $e->getMessage()
        ], 500);
    }
}
    

    public function salarySheet(Request $request)
    {
        $month = $request->month ? Carbon::parse($request->month) : now();
        
        // Get payrolls for the selected month (25th previous month to 24th current month)
        $startDate = $month->copy()->subMonth()->day(25);
        $endDate = $month->copy()->day(24);
        
        $payrolls = Payroll::with(['employee.user', 'employee'])
            ->where('pay_period_start', $startDate)
            ->where('pay_period_end', $endDate)
            ->orderBy('pay_period_start', 'desc')
            ->get();
        
        return view('payroll.salarySheet', [
            'payrolls' => $payrolls,
            'selectedMonth' => $month->format('Y-m')
        ]);
    }

    public function exportSalarySheet(Request $request)
    {
        $month = Carbon::parse($request->month);
        $startDate = $month->copy()->subMonth()->day(25);
        $endDate = $month->copy()->day(24);
    
        $payrolls = Payroll::with(['employee.user', 'employee'])
            ->where('pay_period_start', $startDate)
            ->where('pay_period_end', $endDate)
            ->get();
    
        $format = $request->format;
    
        if ($format == 'csv') {
            $filename = "Salary-Sheet-{$month->format('F-Y')}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
            ];
    
            $callback = function() use ($payrolls) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'SL', 'Employee Name', 'Department', 'Position',
                    'Base Salary', 'Bonuses', 'Commission', 'Transport',
                    'Others', 'Deductions', 'Net Salary', 'Status'
                ]);
    
                // CSV Data
                $sl = 1;
                foreach ($payrolls as $payroll) {
                    fputcsv($file, [
                        $sl++,
                        $payroll->employee->user->name ?? 'N/A',
                        $payroll->employee->department ?? '-',
                        $payroll->employee->position ?? '-',
                        number_format($payroll->base_salary, 2),
                        number_format($payroll->bonuses, 2),
                        number_format($payroll->commission, 2),
                        number_format($payroll->transport, 2),
                        number_format($payroll->others, 2),
                        number_format($payroll->deductions, 2),
                        number_format($payroll->net_salary, 2),
                        ucfirst($payroll->payment_status)
                    ]);
                }
    
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
        }
    
        if ($format == 'pdf') {
            $pdf = PDF::loadView('payroll.salarySheetPDF', [
                'payrolls' => $payrolls,
                'month' => $month,
                'startDate' => $startDate,
                'endDate' => $endDate
            ])->setPaper('a4', 'landscape');
    
            return $pdf->download("Salary-Sheet-{$month->format('F-Y')}.pdf");
        }
    
        return back()->with('error', 'Invalid export format.');
    }
    



    public function downloadCashSignatureSheet(Request $request)
    {
        $month = Carbon::parse($request->month);
        $startDate = $month->copy()->subMonth()->day(25);
        $endDate = $month->copy()->day(24);

        $payrolls = Payroll::with(['employee.user'])
            ->where('pay_period_start', $startDate)
            ->where('pay_period_end', $endDate)
            ->orderBy('net_salary', 'desc')
            ->get();

        $format = $request->format;
        $total = $payrolls->sum('net_salary');
        $monthName = $month->format("F'Y");

        if ($format == 'csv') {
            $filename = "Cash-Signature-Sheet-{$month->format('F-Y')}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
            ];

            $callback = function() use ($payrolls, $monthName, $total) {
                $file = fopen('php://output', 'w');
                
                // Title
                fputcsv($file, ["Cash Salary for the Month of {$monthName} of Tech Cloud AUS Project."]);
                fputcsv($file, []); // Empty row
                
                // Header
                fputcsv($file, ['SL', 'ID', 'Name', 'BDT', 'Signature & Date']);
                
                // Data
                $sl = 1;
                foreach ($payrolls as $payroll) {
                    fputcsv($file, [
                        $sl++,
                        $payroll->employee->employee_id ?? 'N/A',
                        $payroll->employee->user->name ?? 'N/A',
                        number_format($payroll->net_salary, 2),
                        '' // Empty for signature
                    ]);
                }
                
                // Total
                fputcsv($file, []);
                fputcsv($file, ['Total', '', '', number_format($total, 2)]);
                
                // Footer
                fputcsv($file, []);
                fputcsv($file, ['Prepared By', 'Checked By']);
                fputcsv($file, ['S.M.Syful Islam', 'Mahdy Hasan']);
                fputcsv($file, ['Consultant', 'Head of Growth']);
                fputcsv($file, ['Tech Cloud Ltd.', 'Tech Cloud Ltd.']);
                fputcsv($file, []);
                fputcsv($file, ['Approved By']);
                fputcsv($file, ['SJ Tarique']);
                fputcsv($file, ['Managing Director']);
                fputcsv($file, ['Tech Cloud Ltd.']);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($format == 'pdf') {
            $pdf = PDF::loadView('payroll.cashSignatureSheetPDF', [
                'payrolls' => $payrolls,
                'monthName' => $monthName,
                'total' => $total
            ])->setPaper('a4', 'portrait');

            return $pdf->download("Cash-Signature-Sheet-{$month->format('F-Y')}.pdf");
        }

        return back()->with('error', 'Invalid export format.');
    }




}