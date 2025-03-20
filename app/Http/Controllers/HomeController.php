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
use App\Models\InvoiceItem;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */



     public function index()
     {
         if (Auth::user()->user_type_id == 1) { // SuperAdmin
             return $this->superAdminDashboard();
         } else {
             return $this->userDashboard(); // Other users go to user dashboard
         }
     }
     
     // SuperAdmin Dashboard
     public function superAdminDashboard(Request $request = null)
{
    // If no request is passed, use the current request
    $request = $request ?? request();

    // Default date range (e.g., last 30 days)
    $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
    $endDate = $request->input('end_date', Carbon::now()->toDateString());

    // Validate date range
    if ($startDate > $endDate) {
        return redirect()->back()->with('error', 'Invalid date range.');
    }

    // General Statistics
    $totalEmployees = Employee::count();
    $totalClients = Client::count();
    $totalTransactions = Transaction::count();
    $totalInvoices = Invoice::count();
    $totalEarnings = Transaction::where('type', 'Deposit')->sum('amount');
    $totalExpenses = Expense::sum('amount');
    $bankBalance = BankAccount::sum('balance');

    // Monthly Earnings and Expenses
    $monthlyEarnings = Transaction::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
        ->where('type', 'Deposit')
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->pluck('total', 'month')->toArray();

    $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->pluck('total', 'month')->toArray();

    // Transaction and Invoice Counts
    $depositCount = Transaction::where('type', 'Deposit')->count();
    $withdrawalCount = Transaction::where('type', 'Withdrawal')->count();
    $invoiceCount = Invoice::count();

    // Recent Transactions and Invoices
    $recentTransactions = Transaction::latest()->take(5)->get();
    $recentInvoices = Invoice::with('client')->latest()->take(5)->get();

    // Attendance Summary
    $attendanceSummary = Attendance::whereBetween('date', [$startDate, $endDate])
        ->selectRaw('
            COUNT(CASE WHEN status_id = 1 THEN 1 END) as total_present,
            COUNT(CASE WHEN status_id = 2 THEN 1 END) as total_absent,
            COUNT(CASE WHEN isLate = 1 THEN 1 END) as total_late,
            COUNT(*) as total_attendance
        ')
        ->first();

    // Expense Summary
    $expenseSummary = [
        'totalExpenses' => Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount'),
        'expensesByCategory' => Expense::selectRaw('category_id, SUM(amount) as total')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->with('expenseCategory')
            ->get(),
    ];

    // Transactions and Invoices for Date Range
    $filteredTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
        ->latest()
        ->get();

    $filteredInvoices = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
        ->with('client')
        ->latest()
        ->get();

    return view('dashboard', compact(
        'totalEmployees', 'totalClients', 'totalTransactions', 'totalInvoices',
        'totalEarnings', 'totalExpenses', 'bankBalance', 'monthlyEarnings',
        'monthlyExpenses', 'depositCount', 'withdrawalCount', 'invoiceCount',
        'recentTransactions', 'recentInvoices', 'attendanceSummary', 'expenseSummary',
        'filteredTransactions', 'filteredInvoices', 'startDate', 'endDate'
    ));
}






     // User Dashboard
   private function userDashboard()
{
    $user = Auth::user();
    $employee = Employee::where('user_id', $user->id)->first();

    if (!$employee) {
        return redirect()->route('home')->with('error', 'Employee record not found.');
    }

    if (!$user->isUser()) {
        return redirect()->route('home')->with('error', 'Access denied.');
    }

    // Fetch attendance details
    $totalPresent = Attendance::where('employee_id', $employee->id)
        ->where('status_id', 1) // Assuming 1 is the ID for "Present"
        ->count();

    $totalAbsent = Attendance::where('employee_id', $employee->id)
        ->where('status_id', 2) // Assuming 2 is the ID for "Absent"
        ->count();

    $totalLate = Attendance::where('employee_id', $employee->id)
        ->where('isLate', 1)
        ->count();

    $todayAttendance = Attendance::where('employee_id', $employee->id)
        ->where('date', Carbon::today()->toDateString())
        ->first();

    // Fetch sales data if applicable
    $totalSales = EmployeeSales::where('employee_id', $employee->id)
        ->sum('sales_amount');

    $totalSalesQty = EmployeeSales::where('employee_id', $employee->id)
        ->sum('sales_qty');

    return view('dashboard_user', compact(
        'user',
        'employee',
        'totalPresent',
        'totalAbsent',
        'totalLate',
        'todayAttendance',
        'totalSales',
        'totalSalesQty'
    ));
}

    // SALES SUMMARY FOR DILLON
    public function salesSummaryDillon(Request $request)
    {
        // Fetch all clients for dropdown
        $clients = Client::all();
    
        // Default to today's date if no date is provided
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $clientId = $request->input('client_id');
    
        // Ensure absentEmployees is always initialized
        $absentEmployees = collect();
    
        // If a client is selected, fetch employees for that client
        $employees = Employee::with('user')
            ->where('client_id', $clientId)
            ->get();
    
        // Fetch attendance records for the selected date range
        $attendance = Attendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee.user')
            ->get();
    
        // Fetch sales records for employees who have a check-in time
        $sales = EmployeeSales::whereIn('employee_id', $attendance->pluck('employee_id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['employee.user'])
            ->get();
    
        // Compute absent employees
        if ($clientId) {
            // Get employees who are NOT in attendance
            $presentEmployeeIds = $attendance->pluck('employee_id')->unique();
            $absentEmployees = $employees->whereNotIn('id', $presentEmployeeIds);
        }
    
        return view('sales_summary', compact(
            'clients', 'employees', 'attendance', 'sales', 'absentEmployees', 'startDate', 'endDate', 'clientId'
        ));
    }
    
     







}
