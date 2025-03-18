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
class EmployeeController extends Controller
{
    
    public function index() {
        if (Auth::user()->isSuperAdmin()) {
            $employees = Employee::with(['client', 'user' => function ($query) {
                $query->whereNotNull('name'); 
            }])->get();
            $clients = Client::all();
            $users = User::all();
            return view('employees.index', compact('employees', 'clients', 'users'));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'stage_name' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'salary_amount' => 'nullable|numeric',
            'salary_type' => 'nullable|string|in:monthly,hourly',
            'date_of_hire' => 'nullable|date',
            'login_time' => 'nullable|date_format:H:i', 
        ]);

        Employee::create($request->only([
            'user_id', 'client_id', 'department', 'position', 
            'salary_amount', 'salary_type', 'date_of_hire', 'login_time'
        ]));

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'stage_name' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'salary_amount' => 'nullable|numeric',
            'salary_type' => 'nullable|string|in:monthly,hourly',
            'date_of_hire' => 'nullable|date',
            'date_of_termination' => 'nullable|date',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'resume_cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'notes' => 'nullable|string',
            'login_time' => 'nullable|date_format:H:i',
        ]);
    
        $employee = Employee::findOrFail($id);
        
        // Handle resume file upload
        if ($request->hasFile('resume_cv')) {
            $file = $request->file('resume_cv');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store the file in Laravel storage
            $file->storeAs('public/resumes', $filename);  
    
            // Save the new resume file name to the employee record
            $employee->resume_cv = $filename;
        }
    
        // Update other fields
        $employee->update($request->except('resume_cv'));
    
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }  

    
    
    public function edit($id)
    {
        $employee = Employee::with('user', 'client')->findOrFail($id);  
        $clients = Client::all();
        $users = User::all();

        return view('employees.edit', compact('employee', 'clients', 'users'));
    }

    


    public function employeeProfile(Request $request)
    {
        // Ensure the logged-in user can only view their own profile
        $employee = Auth::user()->employee()->firstOrFail();
        
        return view('employees.profile', compact('employee'));
    }


    public function updateEmployeeProfile(Request $request)
    {
        // Ensure the logged-in user can only update their own profile
        $employee = Auth::user()->employee()->firstOrFail();

        // Validate the request
        $request->validate([
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'married' => 'nullable|string',
            'nid_number' => 'nullable|string',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'resume_cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
    
        ]);

        // Handle resume file upload
        if ($request->hasFile('resume_cv')) {
            $file = $request->file('resume_cv');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/resumes', $filename); // Store in storage/app/public/resumes

            // Delete old file if it exists
            if ($employee->resume_cv) {
                $oldFilePath = storage_path('app/public/resumes/' . $employee->resume_cv);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Delete the old file
                }
            }

            // Save the new file path in the database
            $employee->resume_cv = $filename;
        }

        // Update other fields
        $employee->update($request->except('resume_cv'));

        return redirect()->back()->with('success', 'Employee details updated successfully.');
    }    



    public function salesReport(Request $request)
    {
        // Fetch all employees and clients for dropdowns
        $employees = Employee::with('user')->get();
        $clients = Client::all();

        // Initialize the query
        $salesQuery = EmployeeSales::with(['employee.user', 'client']);

        // Apply filters based on request inputs
        if ($request->filled('start_date')) {
            $salesQuery->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $salesQuery->where('date', '<=', $request->end_date);
        }

        if ($request->filled('employee_id')) {
            $salesQuery->where('employee_id', $request->employee_id);
        }

        if ($request->filled('client_id')) {
            $salesQuery->where('client_id', $request->client_id);
        }

        // Get the filtered results while ensuring valid employee and client relationships
        $sales = $salesQuery
            ->whereHas('employee') // Ensures employee exists
            ->whereHas('client') // Ensures client exists
            ->get();

        // Fetch attendance data for the selected date range
        $attendance = [];
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $attendance = Attendance::whereBetween('date', [$request->start_date, $request->end_date])
                ->with('employee.user')
                ->get();
        }

        return view('sales_report.index', compact('sales', 'employees', 'clients', 'attendance'));
    }





}
