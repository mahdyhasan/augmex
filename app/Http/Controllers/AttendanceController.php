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
use App\Models\Transaction;
use App\Models\User;


class AttendanceController extends Controller
{
    public function index()
    {
        if (Auth::user()->isSuperAdmin()) {
            $attendances = Attendance::with('employee.user')->orderBy('date', 'desc')->get();
            $employees = Employee::with('user', 'sales')->get();
        } else {
            $attendances = Attendance::with('employee.user')
                ->whereHas('employee', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderBy('date', 'desc')
                ->get();
            $employees = Employee::where('user_id', Auth::id())->with('user', 'sales')->get();
        }

        return view('attendances.index', compact('attendances', 'employees'));
    }


    public function clockIn() {

        return view('attendances.clockIn');
        
    }


    public function logIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $today = Carbon::today();
        $dayOfWeek = $today->format('l'); // Get day name (e.g., "Saturday")

        // Check if today is a weekend (Saturday or Sunday)
        if ($dayOfWeek === 'Saturday' || $dayOfWeek === 'Sunday') {
            return redirect()->back()->with('error', 'Today is a weekend. No attendance required.');
        }

        // Check if already logged in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You have already logged in today.');
        }

        // Get login time from employee record
        $expectedLoginTime = Carbon::parse($employee->login_time);
        $actualCheckInTime = Carbon::now()->setTimezone('Asia/Dhaka');

        // Determine if the employee is late
        $isLate = $actualCheckInTime->gt($expectedLoginTime->addMinutes(10)) ? 1 : 0;

        // Log the check-in
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today->toDateString(),
            'check_in' => $actualCheckInTime->toTimeString(),
            'status' => 'Present',
            'isLate' => $isLate, // Store whether the employee was late
            'created_at' => now(),
            'updated_at' => now()
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

        // Validate sales data
        $request->validate([
            'sales_qty' => 'required|integer|min:0',
            'sales_amount' => 'required|numeric|min:0'
        ]);

        // Check if sales entry exists for the employee on the same day
        $existingSales = EmployeeSales::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingSales) {
            // Update existing sales record
            $existingSales->update([
                'sales_qty' => $request->sales_qty,
                'sales_amount' => $request->sales_amount
            ]);
        } else {
            // Create a new sales record if none exists
            EmployeeSales::create([
                'client_id' => $employee->client_id,
                'employee_id' => $employee->id,
                'date' => $today,
                'sales_qty' => $request->sales_qty,
                'sales_amount' => $request->sales_amount
            ]);
        }

        // Update attendance check-out
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            $existingAttendance->update([
                'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString()
            ]);
        } else {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString(),
                'status' => 'Absent',
                'updated_at' => now()
            ]);
        }

        Auth::logout();
        return redirect('/login')->with('success', 'Logged out successfully. Sales entry recorded.');
    }




    public function edit($id)
    {
        $attendance = Attendance::with('employee.user')->findOrFail($id);
        return view('attendances.edit', compact('attendance'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late',
        ]);
    
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
        ]);
    
        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully!');
    }
    



    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s|after_or_equal:check_in',
            'status' => 'required|in:Present,Absent,Late,Weekend,Holiday',
        ]);
    
        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
        ]);
    
        return redirect()->route('attendance.index')->with('success', 'Attendance added successfully!');
    }
    

    
}