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
use App\Models\Leave;
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
        $payrolls = Payroll::with('employee', 'employee.user', 'employee.leaves')->orderBy('pay_period_start', 'desc')->get();
        $employees = Employee::with('user')->get(); // Fetch employees for the dropdown
        return view('payroll.index', compact('payrolls', 'employees'));
    }

    public function edit($id)
    {
        $payroll = Payroll::with([
            'employee.user',
            'employee.leaves' => function($query) use ($id) {
                $payroll = Payroll::find($id);
                $query->where('approved', 1)
                      ->where(function($q) use ($payroll) {
                          $q->whereBetween('start_date', [$payroll->pay_period_start, $payroll->pay_period_end])
                            ->orWhereBetween('end_date', [$payroll->pay_period_start, $payroll->pay_period_end]);
                      });
            },
            'employee.leaves.status'
        ])->findOrFail($id);
        
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
                // Validate input
                $validated = $request->validate([
                    'month' => 'required|string|date_format:Y-m'
                ]);
                
                // Parse dates
                $selectedMonth = Carbon::parse($validated['month']);
                $startDate = $selectedMonth->copy()->subMonth()->day(25);
                $endDate = $selectedMonth->copy()->day(24);

                // Currency rates
                $currencyRates = [
                    'AUD' => 75,
                    'EUR' => 130, 
                    'GBP' => 145,
                    'USD' => 120
                ];

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
                        // Skip employees without salary
                        if (empty($employee->salary_amount)) {
                            $errors[] = "Skipped: Employee {$employee->user->name} has no salary amount set";
                            continue;
                        }

                        // 1. Calculate working days
                        $totalWorkingDays = $this->calculateWorkingDays($startDate, $endDate);

                        // 2. Process leaves
                        $leaveData = $this->processLeaves($employee, $startDate, $endDate);
                        $leaveDaysCount = $leaveData['leaveDays'];
                        $lwpDaysCount = $leaveData['lwpDays'];

                        // 3. Calculate attendance
                        $attendanceCount = Attendance::where('employee_id', $employee->id)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->whereNotNull('check_in')
                            ->whereRaw('WEEKDAY(date) < 5')
                            ->count();

                        // 4. Calculate deductions
                        $effectivePresentDays = $attendanceCount + $leaveDaysCount;
                        $absentDays = max(0, $totalWorkingDays - $effectivePresentDays - $lwpDaysCount);
                        // $perDaySalary = $totalWorkingDays > 0 ? $employee->salary_amount / $totalWorkingDays : 0;
                        $perDaySalary = $employee->salary_amount / 30; // Always divide by 30 days
                        $deduction = ($absentDays + $lwpDaysCount) * $perDaySalary;

                        // 5. Calculate commission
                        // $totalCommission = $this->calculateCommission($employee, $startDate, $endDate, $currencyRates);
                        $totalCommission = 0;

                        // 6. Calculate net salary
                        $netSalary = $employee->salary_amount + $totalCommission - $deduction;

                        // 7. Prepare payroll data
                        $payrollData = [
                            'year' => $selectedMonth->year,
                            'month' => $selectedMonth->month,
                            'employee_id' => $employee->id,
                            'base_salary' => $employee->salary_amount,
                            'commission' => $totalCommission,
                            'deductions' => $deduction,
                            'net_salary' => $netSalary,
                            'payment_status' => 'pending',
                            'pay_period_start' => $startDate->format('Y-m-d'),
                            'pay_period_end' => $endDate->format('Y-m-d'),
                            'bonuses' => 0,
                            'transport' => 0,
                            'others' => 0
                        ];

                        // 8. Update or create payroll record
                        $existing = Payroll::where('employee_id', $employee->id)
                            ->where('pay_period_start', $startDate->format('Y-m-d'))
                            ->where('pay_period_end', $endDate->format('Y-m-d'))
                            ->first();

                        if ($existing) {
                            // Preserve existing values
                            $payrollData['bonuses'] = $existing->bonuses;
                            $payrollData['transport'] = $existing->transport;
                            $payrollData['others'] = $existing->others;
                            $payrollData['payment_status'] = $existing->payment_status;
                            $payrollData['payment_date'] = $existing->payment_date;
                            $updatedCount++;
                        } else {
                            $generatedCount++;
                        }

                        $result = Payroll::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'pay_period_start' => $startDate->format('Y-m-d'),
                                'pay_period_end' => $endDate->format('Y-m-d')
                            ],
                            $payrollData
                        );

                        if (!$result->wasRecentlyCreated && !$result->wasChanged()) {
                            $errors[] = "No changes for {$employee->user->name}";
                        }

                    } catch (\Exception $e) {
                        $errors[] = "Error processing {$employee->user->name}: " . $e->getMessage();
                        \Log::error("Payroll error for {$employee->id}: " . $e->getMessage());
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
                \Log::error("Payroll system error: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'System error: ' . $e->getMessage()
                ], 500);
            }
        }

        // Helper methods
        private function calculateWorkingDays(Carbon $start, Carbon $end): int
        {
            $days = 0;
            $current = $start->copy();
            
            while ($current <= $end) {
                if ($current->isWeekday()) {
                    $days++;
                }
                $current->addDay();
            }
            return $days;
        }

        private function processLeaves(Employee $employee, Carbon $start, Carbon $end): array
        {
            $leaves = Leave::with('status')
                ->where('employee_id', $employee->id)
                ->where('approved', 1)
                ->where(function($query) use ($start, $end) {
                    $query->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function($q) use ($start, $end) {
                            $q->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->get();

            $leaveDays = 0;
            $lwpDays = 0;

            foreach ($leaves as $leave) {
                $periodStart = max($leave->start_date, $start);
                $periodEnd = min($leave->end_date, $end);
                
                $current = $periodStart->copy();
                while ($current <= $periodEnd) {
                    if ($current->isWeekday()) {
                        $leave->status->name === 'LWP' ? $lwpDays++ : $leaveDays++;
                    }
                    $current->addDay();
                }
            }

            return ['leaveDays' => $leaveDays, 'lwpDays' => $lwpDays];
        }

        private function calculateCommission(Employee $employee, Carbon $start, Carbon $end, array $rates): float
        {
            $commission = DivanjCommission::where('employee_id', $employee->id)
                ->whereBetween('start_date', [$start, $end])
                ->get()
                ->sum(function($record) use ($employee, $rates) {
                    $client = ClientCondition::where('client_id', $employee->client_id)->first();
                    $currency = $client->currency ?? 'AUD';
                    $rate = $rates[strtoupper($currency)] ?? 1;
                    return $record->commission_amount * $rate;
                });

            return (float)$commission;
        }


  

    public function showDeductions(Payroll $payroll)
    {
        // Calculate working days
        $start = Carbon::parse($payroll->pay_period_start);
        $end = Carbon::parse($payroll->pay_period_end);
        
        $workingDays = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->isWeekday()) {
                $workingDays++;
            }
            $current->addDay();
        }
        
        // Get attendance data
        $attendanceCount = Attendance::where('employee_id', $payroll->employee_id)
            ->whereBetween('date', [$start, $end])
            ->whereNotNull('check_in')
            ->whereRaw('WEEKDAY(date) < 5')
            ->count();
        
        // Get approved leaves
        $leaves = Leave::with('status')
            ->where('employee_id', $payroll->employee_id)
            ->where('approved', 1)
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();
        
        // Calculate leave days
        $lwpDays = 0;
        $paidLeaveDays = 0;
        $leaveDetails = [];
        
        foreach ($leaves as $leave) {
            $periodStart = max($leave->start_date, $start);
            $periodEnd = min($leave->end_date, $end);
            $days = 0;
            
            $currentDay = $periodStart->copy();
            while ($currentDay <= $periodEnd) {
                if ($currentDay->isWeekday()) {
                    $days++;
                    $leave->status->name === 'LWP' ? $lwpDays++ : $paidLeaveDays++;
                }
                $currentDay->addDay();
            }
            
            $leaveDetails[] = [
                'type' => $leave->status->name,
                'start' => $leave->start_date->format('d M Y'),
                'end' => $leave->end_date->format('d M Y'),
                'days' => $days,
                'status' => $leave->status->name === 'LWP' ? 'Unpaid' : 'Paid'
            ];
        }
        
        // Calculate deductions
        // $perDaySalary = $workingDays > 0 ? $payroll->base_salary / $workingDays : 0;
        $perDaySalary = $payroll->base_salary / 30; // Always divide by 30 days
        $effectivePresentDays = $attendanceCount + $paidLeaveDays;
        $absentDays = max(0, $workingDays - $effectivePresentDays - $lwpDays);
        
        $deductionBreakdown = [
            'lwp_deduction' => $lwpDays * $perDaySalary,
            'absence_deduction' => $absentDays * $perDaySalary,
            'total_deduction' => $payroll->deductions
        ];
        
        return view('payroll.deductions', compact(
            'payroll',
            'workingDays',
            'attendanceCount',
            'leaveDetails',
            'lwpDays',
            'paidLeaveDays',
            'absentDays',
            'perDaySalary',
            'deductionBreakdown'
        ));
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