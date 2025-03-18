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
     private function superAdminDashboard()
     {
        
         $totalEmployees = Employee::count();
         $totalClients = Client::count();
         $totalTransactions = Transaction::count();
         $totalInvoices = Invoice::count();
         $totalEarnings = Transaction::where('type', 'Deposit')->sum('amount');
         $totalExpenses = Expense::sum('amount');
         $bankBalance = BankAccount::sum('balance');
     
         $monthlyEarnings = Transaction::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
             ->where('type', 'Deposit')
             ->groupBy('month')
             ->orderBy('month', 'ASC')
             ->pluck('total', 'month')->toArray();
     
         $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
             ->groupBy('month')
             ->orderBy('month', 'ASC')
             ->pluck('total', 'month')->toArray();
     
         $depositCount = Transaction::where('type', 'Deposit')->count();
         $withdrawalCount = Transaction::where('type', 'Withdrawal')->count();
         $invoiceCount = Invoice::count();
     
         $recentTransactions = Transaction::latest()->take(5)->get();
         $recentInvoices = Invoice::with('client')->latest()->take(5)->get();
     
         return view('dashboard', compact(
             'totalEmployees', 'totalClients', 'totalTransactions', 'totalInvoices',
             'totalEarnings', 'totalExpenses', 'bankBalance', 'monthlyEarnings',
             'monthlyExpenses', 'depositCount', 'withdrawalCount', 'invoiceCount',
             'recentTransactions', 'recentInvoices'
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
        $totalPresent = Attendance::where('employee_id', $employee->id)->where('status', 'Present')->count();
        $totalAbsent = Attendance::where('employee_id', $employee->id)->where('status', 'Absent')->count();
        $totalLate = Attendance::where('employee_id', $employee->id)->where('isLate', 1)->count();
        $todayAttendance = Attendance::where('employee_id', $employee->id)->where('date', Carbon::today()->toDateString())->first();

        // Fetch sales data if applicable
        $totalSales = EmployeeSales::where('employee_id', $employee->id)->sum('sales_amount'); 
        $totalSalesQty = EmployeeSales::where('employee_id', $employee->id)->sum('sales_qty'); 

        return view('dashboard_user', compact('user', 'employee', 'totalPresent', 'totalAbsent', 'totalLate', 'todayAttendance', 'totalSales', 'totalSalesQty'));
    }



     public function salesSummaryDillon(Request $request)
     {

         // Fetch all clients for dropdown
         $clients = Client::all();
         $date = $request->input('date') ?? now()->toDateString(); // Default to today's date
         $clientId = $request->input('client_id');
     
         // Fetch employees who belong to the selected client
         $employees = Employee::with('user')
             ->where('client_id', $clientId)
             ->get();
     
         // Fetch attendance records of those employees on the selected date
         $attendance = Attendance::whereIn('employee_id', $employees->pluck('id'))
             ->whereDate('date', $date)
             ->with('employee.user')
             ->get();
     
         // Fetch sales records of those employees who have a check-in time
         $sales = EmployeeSales::whereIn('employee_id', $attendance->pluck('employee_id'))
             ->whereDate('date', $date)
             ->with(['employee.user'])
             ->get();
     
         return view('sales_summary', compact('clients', 'employees', 'attendance', 'sales', 'date', 'clientId'));
     }
     







}
