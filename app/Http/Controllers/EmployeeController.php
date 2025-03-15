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
use App\Models\UserType;

class EmployeeController extends Controller
{
    
    public function listAllEmployees() {
        if (Auth::user()->isSuperAdmin()) {
            $employees = Employee::all();
            return view('employees.list', compact('employees'));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
    }
    


    public function employeeProfile (Request $request) {

        if (Auth::user()->isSuperAdmin() && $request->has('employee_id')) {
            // SuperAdmin can view any employee's details
            $employee = Employee::findOrFail($request->employee_id);
        } else {
            // Regular employees can only view their own details
            $employee = Auth::user()->employee()->firstOrFail();
        }

        $employeeDetail = EmployeeDetail::where('employee_id', $employee->id)->first();

        return view('employees.profile', compact('employee', 'employeeDetail'));
        
    }


    public function updateEmployeeProfile (Request $request)
    {
        // Identify the employee based on user role
        if (Auth::user()->isSuperAdmin() && $request->has('employee_id')) {
            $employee = Employee::findOrFail($request->employee_id);
        } else {
            $employee = Auth::user()->employee()->firstOrFail();
        }

        // Find or create employee details
        $employeeDetail = EmployeeDetail::updateOrCreate(
            ['employee_id' => $employee->id],
            $request->except('_token', 'resume_cv') // Exclude file from mass update
        );

        // Handle resume file upload
        if ($request->hasFile('resume_cv')) {
            $file = $request->file('resume_cv');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Store the file in the public/assets/resumes directory
            $path = $file->move(public_path('assets/resumes'), $filename);

            // Check if the file was uploaded successfully
            if ($path) {
                // Delete old file if exists
                if ($employeeDetail->resume_cv) {
                    $oldFilePath = public_path('assets/resumes/' . $employeeDetail->resume_cv);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Delete the old file
                    }
                }

                // Update resume path in database
                $employeeDetail->resume_cv = $filename;
                $employeeDetail->save();
            }
        }

        return redirect()->back()->with('success', 'Employee details updated successfully.');
    
    }
    







}
