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

class EmployeeController extends Controller
{
    public function listAllEmployees() {
        $employees = Employee::paginate(10);
        return view('employees.index', compact('employees'));
    }
    public function viewEmployeeProfile($id) {}
    public function registerNewEmployee(Request $request) {}
    public function updateEmployeeDetails(Request $request, $id) {}
    public function removeEmployee($id) {}
}
