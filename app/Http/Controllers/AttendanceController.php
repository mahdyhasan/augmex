<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;


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
use App\Models\Leave;
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
    

    // DELETE ATTENDANCE
    public function deleteFiltered(Request $request)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isHR()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }
    
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $employeeName = $request->input('employee_name');
    
        $query = Attendance::query();
    
        // Apply the same filters as the index method
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }
    
        if ($employeeName) {
            $query->whereHas('employee.user', function ($q) use ($employeeName) {
                $q->where('name', 'like', '%' . $employeeName . '%');
            });
        }
    
        $count = $query->count();
    
        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No matching records found to delete.'
            ]);
        }
    
        try {
            $query->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$count} attendance record(s)."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete records: ' . $e->getMessage()
            ], 500);
        }
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
    


    






    // LEAVE MANAGEMENT

    // Display all leaves (for HR/Admin) or employee's own leaves
    public function leavesIndex(Request $request)
    {
        $user = auth()->user();
        $isHR = $user->isSuperAdmin() || $user->isHR();
        
        $leaves = Leave::with(['employee.user', 'status'])
            ->when(!$isHR, fn($q) => $q->where('employee_id', $user->employee->id))
            ->when($isHR && $request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->approved !== null, fn($q) => $q->where('approved', $request->approved))
            ->latest()
            ->get();
    
        // Always pass employees, but for non-HR, pass only their own employee record
        $employees = $isHR 
            ? Employee::with('user')->get() 
            : collect([$user->employee]);
    
        return view('attendances.leaves', [
            'leaves' => $leaves,
            'employees' => $employees,
            'leaveTypes' => AttendanceStatus::whereIn('name', ['LWP', 'Sick', 'Casual', 'Holiday'])->get()
        ]);
    }
    
    // Store new leave application
    public function storeLeave(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'status_id' => 'required|exists:attendance_statuses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'approved' => 'required|integer|in:0,1' // Ensures only 0 or 1 is accepted
        ]);
    
        // For regular employees, force pending status
        $user = Auth::user();
        if (!($user->isSuperAdmin() || $user->isHR())) {
            $validated['approved'] = 0;
            $validated['employee_id'] = $user->employee->id; // Ensure they can only apply for themselves
        }
    
        // Rest of your leave creation logic...
        $leave = Leave::create($validated);
    
        return redirect()->route('attendance.leaves')
            ->with('success', 'Leave application submitted successfully!');
    }
    
    


    // APPROVE LEAVE
    public function approveLeave(Leave $leave)
    {
        if (!(Auth::user()->isSuperAdmin() || Auth::user()->isHR())) {
            abort(403);
        }
        
        // Use DB transaction to ensure data consistency
        DB::transaction(function () use ($leave) {
            // Refresh the model first to ensure we have latest data
            $leave->refresh();
            
            if ($leave->approved) {
                throw new \Exception('Leave is already approved.');
            }
            
            // Update using save() instead of update() to ensure model events fire
            $leave->approved = true;
            $leave->save();
            
        });
        
        return back()->with('success', 'Leave approved successfully!');
    }
    


    // Delete a leave
    public function destroyLeave(Leave $leave)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!($user->isSuperAdmin() || $user->isHR() )) {
            abort(403);
        }
        
        // If leave was approved, remove attendance records
        
        $leave->delete();
        
        return redirect()->route('attendance.leaves')
            ->with('success', 'Leave deleted successfully!');
    }
    


   
    public function editLeave(Leave $leave)
    {
        if (!(Auth::user()->isSuperAdmin() || Auth::user()->isHR() || Auth::user()->employee->id == $leave->employee_id)) {
            abort(403);
        }
    
        return view('attendances.edit_leave', [
            'leave' => $leave,
            'employees' => Employee::with('user')->get(),
            'leaveTypes' => AttendanceStatus::whereIn('name', ['LWP', 'Sick', 'Casual', 'Holiday'])->get()
        ]);
    }
    
    public function updateLeave(Request $request, Leave $leave)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!($user->isSuperAdmin() || $user->isHR() || $user->employee->id == $leave->employee_id)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'status_id' => 'required|exists:attendance_statuses,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'approved' => 'boolean'
        ]);
        
        // Debug the input
        \Log::info('Updating leave', [
            'input' => $request->all(),
            'validated' => $validated
        ]);
        
        $leave->update($validated);
        
        // Debug the result
        \Log::info('Leave updated', [
            'approved' => $leave->approved,
            'fresh_data' => $leave->fresh()->toArray()
        ]);
        
        return redirect()->route('attendance.leaves')
            ->with('success', 'Leave updated successfully!');
    }





}