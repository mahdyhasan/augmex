<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\EmployeeDetails;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;
use App\Models\Transaction;
use App\Models\User;


class AttendanceController extends Controller
{
    public function listAllAttendance() {
        if (Auth::user()->isSuperAdmin()) {
            $attendances = Attendance::with('employee')->orderBy('date', 'desc')->get();
        } else {
            $attendances = Attendance::with('employee')
                ->whereHas('employee', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderBy('date', 'desc')
                ->get();
        }
        return view('employees.attendance', compact('attendances'));
    }

    public function clockIn() {
        return view('employees.clockIn');
    }

    public function logIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $today = Carbon::today()->toDateString();

        // Check if already logged in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You have already logged in today.');
        }

        // Log the check-in
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString(),
            'status' => 'Present'
        ]);

        return redirect()->back()->with('success', 'Logged in successfully.');
    }

    public function logOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $today = Carbon::today()->toDateString();

        // Check if already logged in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            // Update check-out time
            $existingAttendance->update([
                'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString()
            ]);

            return redirect()->back()->with('success', 'Logged out successfully.');
        } else {
            // If no log-in entry exists, create a new one with only check-out
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString(),
                'status' => 'Absent'
            ]);

            return redirect()->back()->with('error', 'You logged out without logging in first.');
        }
    }
}