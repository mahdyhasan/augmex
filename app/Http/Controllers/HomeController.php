<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Carbon\CarbonPeriod;
use DateTimeZone;


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
    $monthlyEarnings = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
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
    $filteredTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
        ->latest()
        ->get();

    $filteredInvoices = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
        ->with('client')
        ->latest()
        ->get();

    return view('dashboards.admin', compact(
        'totalEmployees', 'totalClients', 'totalTransactions', 'totalInvoices',
        'totalEarnings', 'totalExpenses', 'bankBalance', 'monthlyEarnings',
        'monthlyExpenses', 'depositCount', 'withdrawalCount', 'invoiceCount',
        'recentTransactions', 'recentInvoices', 'expenseSummary',
        'filteredTransactions', 'filteredInvoices', 'startDate', 'endDate'
    ));
}



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

    // Date Ranges
    $currentMonthStart = Carbon::now()->startOfMonth();
    $currentMonthEnd = Carbon::now()->endOfMonth();

    $currentWeekStart = Carbon::now()->startOfWeek();
    $currentWeekEnd = Carbon::now()->endOfWeek();

    $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
    $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

    // Attendance Summary (Current Month)
    $currentMonthAttendance = [
        'present' => Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('status_id', 1) // Present
            ->count(),
        'absent' => Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('status_id', 2) // Absent
            ->count(),
        'late' => Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('isLate', 1)
            ->count(),
    ];

    // Daily Login Times (for potential use in reports)
    $dailyLogins = Attendance::where('employee_id', $employee->id)
        ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
        ->pluck('check_in', 'date');

    // Current Week Sales
    $currentWeekSales = [
        'total_qty' => EmployeeSales::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->sum('sales_qty'),
        'total_amount' => EmployeeSales::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
            ->sum('sales_amount'),
    ];

    // Last Week Sales
    $lastWeekSales = [
        'total_qty' => EmployeeSales::where('employee_id', $employee->id)
            ->whereBetween('date', [$lastWeekStart, $lastWeekEnd])
            ->sum('sales_qty'),
        'total_amount' => EmployeeSales::where('employee_id', $employee->id)
            ->whereBetween('date', [$lastWeekStart, $lastWeekEnd])
            ->sum('sales_amount'),
    ];

    // Sales Trend (Current Month)
    $salesTrendData = [];
    $salesTrendQty = [];
    $salesTrendLabels = [];
    $currentDate = Carbon::now()->startOfMonth();

    while ($currentDate <= Carbon::now()->endOfMonth()) {
        $label = $currentDate->format('M d');

        $salesAmount = EmployeeSales::where('employee_id', $employee->id)
            ->whereDate('date', $currentDate)
            ->sum('sales_amount');

        $salesQty = EmployeeSales::where('employee_id', $employee->id)
            ->whereDate('date', $currentDate)
            ->sum('sales_qty');

        $salesTrendLabels[] = $label;
        $salesTrendData[] = round($salesAmount, 2);
        $salesTrendQty[] = $salesQty;

        $currentDate->addDay();
    }
    
    // Daily Sales for the current month
    $dailySales = [];
    $currentDate = Carbon::now()->startOfMonth();
    
    while ($currentDate <= Carbon::now()->endOfMonth()) {
        $dailySales[] = [
            'date' => $currentDate->format('Y-m-d'),
            'sales_qty' => EmployeeSales::where('employee_id', $employee->id)
                ->whereDate('date', $currentDate)
                ->sum('sales_qty'),
            'sales_amount' => EmployeeSales::where('employee_id', $employee->id)
                ->whereDate('date', $currentDate)
                ->sum('sales_amount'),
        ];
        $currentDate->addDay();
    }


    return view('dashboards.user', compact(
        'user',
        'employee',
        'currentMonthAttendance',
        'currentWeekSales',
        'lastWeekSales',
        'salesTrendData',
        'salesTrendQty',
        'salesTrendLabels',
        'dailyLogins',
        'dailySales'
    ));
}

}