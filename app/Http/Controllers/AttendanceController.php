<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\User;



class AttendanceController extends Controller
{
    public function listAllAttendance() {
        $attendances = Attendance::with('employee')->orderBy('date', 'desc')->get();
        return view('employees.attendance', compact('attendances'));
    }
    public function listEmployeeAttendance() {
    }
    public function recordEmployeeCheckIn(Request $request) {}
    public function recordEmployeeCheckOut(Request $request) {}
    public function updateAttendanceRecord(Request $request, $id) {}
}
