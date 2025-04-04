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
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

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
        $validated = $request->validate([
            // Basic Info
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id',
            'stage_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'married' => 'nullable|string|in:Yes,No',
            'nid_number' => 'nullable|string|max:50',
            
            // Employment Info
            'client_id' => 'nullable|exists:clients,id',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'date_of_hire' => 'required|date',
            'login_time' => 'nullable|date_format:H:i',
            'salary_type' => 'nullable|string',
            'salary_amount' => 'nullable|numeric|min:0',
            
            // Contact Info
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|in:Parent,Spouse,Sibling,Friend,Other',
            'emergency_contact_phone' => 'nullable|string|max:20'
        ]);
    
        Employee::create($validated);
    
        return redirect()->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }


        
        
    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();

            $employee = Employee::findOrFail($id);
            
            // Handle resume file upload
            if ($request->hasFile('resume_cv')) {
                $file = $request->file('resume_cv');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/resumes', $filename);

                if ($employee->resume_cv) {
                    $oldFilePath = storage_path('app/public/resumes/' . $employee->resume_cv);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $employee->resume_cv = $filename;
            }

            // Check if termination date is being set/changed
            $terminationDateChanged = $request->date_of_termination != $employee->date_of_termination;
            $terminationDateBeingSet = $request->date_of_termination && !$employee->date_of_termination;

            // Update user-related fields if they exist
            if ($employee->user) {
                $userData = [];
                
                if ($request->filled('name') || $request->filled('phone')) {
                    $userData['name'] = $request->input('name');
                    $userData['phone'] = $request->input('phone');
                }
                
                // If termination date is being set, deactivate the user
                if ($terminationDateBeingSet) {
                    $userData['status'] = 0; // Set status to inactive
                }
                
                if (!empty($userData)) {
                    $employee->user->update($userData);
                }
            }

            // Update all fields from the request
            $employee->stage_name = $request->stage_name;
            $employee->client_id = $request->client_id;
            $employee->department = $request->department;
            $employee->position = $request->position;
            $employee->salary_amount = $request->salary_amount;
            $employee->salary_type = $request->salary_type;
            $employee->login_time = $request->login_time;
            $employee->date_of_hire = $request->date_of_hire;
            $employee->date_of_termination = $request->date_of_termination;
            $employee->gender = $request->gender;
            $employee->married = $request->married;
            $employee->address_line_1 = $request->address_line_1;
            $employee->address_line_2 = $request->address_line_2;
            $employee->city = $request->city;
            $employee->postal_code = $request->postal_code;
            $employee->country = $request->country;
            $employee->emergency_contact_name = $request->emergency_contact_name;
            $employee->emergency_contact_relationship = $request->emergency_contact_relationship;
            $employee->emergency_contact_phone = $request->emergency_contact_phone;
            $employee->notes = $request->notes;
            $employee->date_of_birth = $request->date_of_birth;
            $employee->nid_number = $request->nid_number;

            $employee->save();

            \DB::commit();
            
            $message = 'Employee updated successfully.';
            if ($terminationDateBeingSet) {
                $message = 'Employee terminated successfully. User account has been deactivated.';
            }
            
            return redirect()
                ->route('employees.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Employee Update Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update employee. Please try again.');
        }
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



    public function show($id)
    {
        $employee = Employee::with(['user', 'client', 'attendances', 'payrolls'])->findOrFail($id);
        return response()->json($employee);
    }
    

    public function attendance(Employee $employee, Request $request)
    {
        $query = $employee->attendances()
            ->with('status')
            ->orderBy('attendances.date', 'DESC');
        
        // Apply filters if requested
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'all':
                    // No additional filtering needed
                    break;
                case 'month':
                    $query->whereBetween('date', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]);
                    break;
                case 'late':
                    $query->where('isLate', true);
                    break;
                case 'absent':
                    $query->whereHas('status', function($q) {
                        $q->where('name', 'like', '%Absent%');
                    });
                    break;
                default:
                    // Default: last 7 days
                    $query->where('date', '>=', now()->subDays(7));
                    break;
            }
        } else {
            // Default: last 30 days
            $query->where('date', '>=', now()->subDays(30));
        }
        
        $attendances = $query->get();
        
        return response()->json([
            'attendance' => $attendances
        ]);
    }



    public function payroll(Employee $employee)
    {
        $payrolls = $employee->payrolls()
            ->orderBy('pay_period_start', 'desc')
            ->get();
        
        return response()->json([
            'payroll' => $payrolls
        ]);
    }



}
