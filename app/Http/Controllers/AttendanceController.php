<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;


use App\Models\Account;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
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
    public function index(Request $request)
    {
        // Get filter parameters from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $employeeName = $request->input('employee_name');
    
        // Base query for attendances
        $attendancesQuery = Attendance::with(['employee.user', 'status'])
            ->orderBy('date', 'desc');
    
        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $attendancesQuery->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $attendancesQuery->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $attendancesQuery->where('date', '<=', $endDate);
        }
    
        // Apply employee name filter if provided
        if ($employeeName) {
            $attendancesQuery->whereHas('employee.user', function ($query) use ($employeeName) {
                $query->where('name', 'like', '%' . $employeeName . '%');
            });
        }
    
        // Fetch attendances based on user role
        if (Auth::user()->isSuperAdmin()) {
            $attendances = $attendancesQuery->get();
            $employees = Employee::with('user', 'sales')->get();
        } else {
            $attendances = $attendancesQuery
                ->whereHas('employee', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->get();
            $employees = Employee::where('user_id', Auth::id())
                ->with('user', 'sales')
                ->get();
        }
    
        $attendanceStatuses = AttendanceStatus::all();
        $employeeList = Employee::all();
    
        return view('attendances.index', compact('attendances', 'employees', 'attendanceStatuses', 'startDate', 'endDate', 'employeeName', 'employeeList'));
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
            'status_id' => 1,
            'isLate' => $isLate, 
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Logged in successfully.');
    }



    // public function logOut(Request $request)
    // {
    //     $user = Auth::user();
    //     $employee = Employee::where('user_id', $user->id)->first();

    //     if (!$employee) {
    //         return redirect()->back()->with('error', 'Employee record not found.');
    //     }

    //     $today = Carbon::today()->toDateString();

    //     // Validate sales data
    //     // $request->validate([
    //     //     'sales_qty' => 'required|integer|min:0',
    //     //     'sales_amount' => 'required|numeric|min:0'
    //     // ]);

    //     // Check if sales entry exists for the employee on the same day
    //     $existingSales = EmployeeSales::where('employee_id', $employee->id)
    //         ->where('date', $today)
    //         ->first();

    //     if ($existingSales) {
    //         // Update existing sales record
    //         $existingSales->update([
    //             'sales_qty' => $request->sales_qty,
    //             'sales_amount' => $request->sales_amount
    //         ]);
    //     } else {
    //         // Create a new sales record if none exists
    //         EmployeeSales::create([
    //             'client_id' => $employee->client_id,
    //             'employee_id' => $employee->id,
    //             'date' => $today,
    //             'sales_qty' => $request->sales_qty,
    //             'sales_amount' => $request->sales_amount
    //         ]);
    //     }

    //     // Update attendance check-out
    //     $existingAttendance = Attendance::where('employee_id', $employee->id)
    //         ->where('date', $today)
    //         ->first();

    //     if ($existingAttendance) {
    //         $existingAttendance->update([
    //             'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString()
    //         ]);
    //     } else {
    //         Attendance::create([
    //             'employee_id' => $employee->id,
    //             'date' => $today,
    //             'check_out' => Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString(),
    //             'status' => 'Absent',
    //             'updated_at' => now()
    //         ]);
    //     }

    //     // Auth::logout();
    //     return redirect()->back()->with('success', 'Logged Out successfully.');
    // }

    public function logOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->setTimezone('Asia/Dhaka')->toTimeString();

        // Check if attendance entry exists for today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            // Update check_out time
            $existingAttendance->update([
                'check_out' => $now,
            ]);
        } else {
            // Create a new attendance entry with check_in and check_out set to the same time
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in' => $now,
                'check_out' => $now,
                'status_id' => 1, // Default status
                'isLate' => 1, // Default to late
            ]);
        }

        // Handle sales data
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

        return redirect()->back()->with('success', 'Logged out successfully.');
    }




    public function store(Request $request)
    {
        

        // Check if an attendance entry already exists for the same employee on the same day
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Attendance entry already exists for this employee on the selected date.');
        }

        // Create a new attendance entry
        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status_id' => $request->status,
            'isLate' => $request->isLate ?? 2,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance added successfully.');
    }
    
    

    public function edit($id)
    {
        $attendance = Attendance::with('employee.user')->findOrFail($id);
        return view('attendances.edit', compact('attendance'));
    }
    

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
           'check_in' => $request->check_in,
           'check_out' => $request->check_out,
            'status_id' => $request->status,
            'isLate' => $request->isLate,
        ]);


    return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully!');
        
    }
    





    
}