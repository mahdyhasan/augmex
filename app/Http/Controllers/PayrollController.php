<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use DataTables;
use Excel;
use PDF; 

use App\Models\Account;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\ClientPayment;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\EmployeeSales;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Liability;
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


    public function generatePayrollForm($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        return response()->json($employee);
    }

    public function generatePayroll(Request $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
        $month = Carbon::parse($request->month);
        $daysInMonth = $month->daysInMonth;
    
        // Get all weekdays in the selected month
        $totalWeekdays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($month->year, $month->month, $day);
            if ($date->isWeekday()) { // Monday to Friday
                $totalWeekdays++;
            }
        }
    
        // Fetch attendance for the employee in the given month
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->whereNotNull('check_in') // Ensure they checked in
            ->count();
    
        // Calculate absent days (only weekdays)
        $absentDays = $totalWeekdays - $attendances;
    
        // Deduction formula: (Total Salary / 30) * Absent Days
        $deduction = ($employee->salary_amount / 30) * $absentDays;
    
        // Calculate net salary
        $netSalary = $employee->salary_amount - $deduction + 
                     ($request->bonuses ?? 0) + 
                     ($request->commission ?? 0) + 
                     ($request->transport ?? 0) + 
                     ($request->others ?? 0);
    

                     
        // Store Payroll
        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'pay_period_start' => $month->startOfMonth(),
            'pay_period_end' => $month->endOfMonth(),
            'year' => $month->year,
            'base_salary' => $employee->salary_amount,
            'bonuses' => $request->bonuses ?? 0,
            'commission' => $request->commission ?? 0,
            'transport' => $request->transport ?? 0,
            'others' => $request->others ?? 0,
            'payment_status' => 'pending',
            'deductions' => $deduction, 
            'net_salary' => $netSalary,
        ]);
    
        return response()->json(['message' => 'Payroll generated successfully!', 'payroll' => $payroll]);
    }


    public function markAsPaid($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update(['payment_status' => 'paid', 'payment_date' => now()]);

        return redirect()->back()->with('success', 'Payroll marked as paid.');
    }



    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        return view('payroll.edit', compact('payroll'));
    }

    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        // Calculate net salary again in case deductions were updated
        $netSalary = $request->base_salary 
                    + ($request->bonuses ?? 0) 
                    + ($request->commission ?? 0) 
                    + ($request->transport ?? 0) 
                    + ($request->others ?? 0) 
                    - ($request->deductions ?? 0);

        $payroll->update([
            'pay_period_start' => \Carbon\Carbon::parse($request->month)->startOfMonth(),
            'pay_period_end' => \Carbon\Carbon::parse($request->month)->endOfMonth(),
            'base_salary' => $request->base_salary,
            'bonuses' => $request->bonuses,
            'commission' => $request->commission,
            'transport' => $request->transport,
            'others' => $request->others,
            'deductions' => $request->deductions,
            'net_salary' => $netSalary,
            'payment_status' => $request->payment_status,
            'payment_date' => $request->payment_status === 'paid' ? $request->payment_date : null,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully.');
    }


    public function salarySheet(Request $request)
    {
        $month = $request->month ? Carbon::parse($request->month) : now();
    
        $payrolls = Payroll::with('employee.user')
            ->whereMonth('pay_period_start', $month->month)
            ->whereYear('pay_period_start', $month->year)
            ->orderBy('pay_period_start', 'desc')
            ->get();
    
        return view('payroll.salarySheet', compact('payrolls'));
    }
    
    public function exportSalarySheet(Request $request)
    {
        $month = Carbon::parse($request->month);
        $payrolls = Payroll::with('employee.user')
            ->whereMonth('pay_period_start', $month->month)
            ->whereYear('pay_period_start', $month->year)
            ->get();

        $format = $request->format;

        if ($format == 'csv') {
            $filename = "Salary-Sheet-{$month->format('F-Y')}.csv";
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Expires" => "0"
            ];

            $handle = fopen('php://output', 'w');
            fputcsv($handle, ["Employee", "Position", "Base Salary", "Bonuses", "Commission", "Transport", "Others", "Deductions", "Net Salary", "Status", "Payment Date"]);

            foreach ($payrolls as $payroll) {
                fputcsv($handle, [
                    $payroll->employee->user->name ?? 'N/A',
                    $payroll->employee->position ?? '-',
                    $payroll->base_salary,
                    $payroll->bonuses,
                    $payroll->commission,
                    $payroll->transport,
                    $payroll->others,
                    $payroll->deductions,
                    $payroll->net_salary,
                    ucfirst($payroll->payment_status),
                    $payroll->payment_date ?? '-'
                ]);
            }

            fclose($handle);
            return response()->streamDownload(function () use ($handle) {
                fclose($handle);
            }, $filename, $headers);
        }

        if ($format == 'pdf') {
            $pdf = Pdf::loadView('payroll.salarySheetPDF', compact('payrolls', 'month'));
            return $pdf->download("Salary-Sheet-{$month->format('F-Y')}.pdf");
        }

        return back()->with('error', 'Invalid export format.');
    }



    public function printSalarySheet(Request $request)
    {
        $month = Carbon::parse($request->month ?? now());
        $payrolls = Payroll::with('employee.user')
            ->whereMonth('pay_period_start', $month->month)
            ->whereYear('pay_period_start', $month->year)
            ->orderBy('pay_period_start', 'desc')
            ->get();
    
        return view('payroll.salarySheetInvoice', compact('payrolls', 'month'));
    }
    


}