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
    
        $today = Carbon::today('Asia/Dhaka');
    
        // Check if already logged in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today->toDateString())
            ->first();
    
        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You have already logged in today.');
        }
    
        // Get expected login time from employee record (assume it's stored in DB as HH:MM:SS)
        $expectedLoginTime = Carbon::parse($employee->login_time, 'Asia/Dhaka');
        $actualCheckInTime = Carbon::now('Asia/Dhaka');
    
        // Determine if late (more than 10 minutes after expected login time)
        $isLate = $actualCheckInTime->gt($expectedLoginTime->copy()->addMinutes(10)) ? 1 : 0;
    
        // Save attendance
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today->toDateString(),
            'check_in' => $actualCheckInTime->toTimeString(),
            'status_id' => 1,
            'isLate' => $isLate,
            'created_at' => now(),
            'updated_at' => now(),
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

        $today = Carbon::today('Asia/Dhaka')->toDateString();
        $now = Carbon::now('Asia/Dhaka')->toTimeString();

        // Check if attendance entry exists for today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // If no check-in was recorded today
        if (!$existingAttendance || !$existingAttendance->check_in) {
            return redirect()->back()->with('error', 'You have not logged in today. Please Log In first and the inform your supervisor.');
        }

        // If already has check-in, update check-out
        $existingAttendance->update([
            'check_out' => $now,
        ]);

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
    


    // LATE SUMMARY
    public function lateSummary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $employeeName = $request->input('employee_name');
    
        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please set a date range and click on the filter first.',
            ]);
        }
    
        // Fetch attendances marked as late
        $records = Attendance::with('employee.user')
            ->where('isLate', 1)
            ->whereBetween('date', [$startDate, $endDate]);
    
        if ($employeeName) {
            $records->whereHas('employee.user', function ($query) use ($employeeName) {
                $query->where('name', 'like', '%' . $employeeName . '%');
            });
        }
    
        $results = [];
        $lateCounts = [];
    
        foreach ($records->get() as $record) {
            $employee = $record->employee;
            $user = $employee->user ?? null;
    
            if (!$employee || !$user || !$record->check_in || !$employee->login_time) {
                continue;
            }
    
            $expected = Carbon::parse($employee->login_time);
            $actual = Carbon::parse($record->check_in);
    
            // Apply 5-minute grace period
            if ($actual->lte($expected->copy()->addMinutes(5))) {
                continue;
            }
    
            $lateBy = $expected->diffInMinutes($actual);
    
            $results[] = [
                'date' => $record->date,
                'name' => $user->name,
                'check_in' => $record->check_in,
                'check_out' => $record->check_out,
                'late_by' => $lateBy,
            ];
    
            // Count late days per employee
            $lateCounts[$user->name] = ($lateCounts[$user->name] ?? 0) + 1;
        }
    
        // Sort late counts descending
        arsort($lateCounts);
    
        return response()->json([
            'status' => 'success',
            'data' => $results,
            'late_summary' => $lateCounts,
        ]);
    }
    


    
}