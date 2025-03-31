<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Carbon\CarbonPeriod;
use DateTimeZone;
use DB;

use DataTables;
use Excel;
use PDF; 

use App\Models\Account;
use App\Models\Attendance;
use App\Models\AttendanceStatus;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\ClientPayment;
use App\Models\DivanjSale;
use App\Models\DivanjCommission;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\EmployeeSales;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

use App\Models\User;
use App\Models\UserType;

use App\Imports\DivanjSalesImport;


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
    $totalTransactions = ClientPayment::count();
    $totalInvoices = Invoice::count();
    $totalExpenses = Expense::sum('amount');
    $bankBalance = BankAccount::sum('balance');
    $totalEarnings = ClientPayment::sum('amount');

    // Monthly Earnings and Expenses
    $monthlyEarnings = ClientPayment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->pluck('total', 'month')->toArray();

    $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->pluck('total', 'month')->toArray();

    // Expenses and Invoice Counts
    $depositCount = ClientPayment::count();
    $withdrawalCount = Expense::count();
    $invoiceCount = Invoice::count();

    // Recent Expenses and Invoices
    $recentTransactions = ClientPayment::latest()->take(5)->get();
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
    $filteredTransactions = ClientPayment::whereBetween('payment_date', [$startDate, $endDate])
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

        if (!$employee || !$user->isUser()) {
            return redirect()->route('home')->with('error', 'Access denied or employee record not found.');
        }

        // Define date ranges
        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $currentWeekStart = $now->copy()->startOfWeek();
        $currentWeekEnd = $now->copy()->endOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

        // Attendance Summary (Current Month)
        $currentMonthAttendance = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->where('status_id', 1)->count(),
            'absent' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->where('status_id', 2)->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->where('isLate', 1)->count(),
        ];

        // Daily logins (optional usage)
        $dailyLogins = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->pluck('check_in', 'date');

        // Sales This Week
        $currentWeekSales = [
            'total_qty' => DivanjSale::where('employee_id', $employee->id)
                ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
                ->sum('quantity'),
            'total_amount' => DivanjSale::where('employee_id', $employee->id)
                ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
                ->sum('total'),
        ];

        // Sales Last Week
        $lastWeekSales = [
            'total_qty' => DivanjSale::where('employee_id', $employee->id)
                ->whereBetween('date', [$lastWeekStart, $lastWeekEnd])
                ->sum('quantity'),
            'total_amount' => DivanjSale::where('employee_id', $employee->id)
                ->whereBetween('date', [$lastWeekStart, $lastWeekEnd])
                ->sum('total'),
        ];

        // Top Sales Day (this week, up to today)
        $topPerformer = DivanjSale::whereBetween('date', [$currentWeekStart, $now])
            ->selectRaw('employee_id, SUM(quantity) as total_qty')
            ->groupBy('employee_id')
            ->orderByDesc('total_qty')
            ->with('employee') // eager load employee for stage_name
            ->first();
    
        $topPerformerFormatted = $topPerformer && $topPerformer->employee
            ? [
                'name' => $topPerformer->employee->stage_name,
                'quantity' => $topPerformer->total_qty,
            ]
            : null;
            
        // Calculate goal based on hiring duration
        $hiredAt = Carbon::parse($employee->date_of_hire);
        $weeksSinceHired = $hiredAt->diffInWeeks($now);
        $goalCases = $weeksSinceHired >= 8 ? 25 : 15;

        // Progress toward goal
        $quantityThisWeek = DivanjSale::where('employee_id', $employee->id)
            ->whereBetween('date', [$currentWeekStart, $now])
            ->sum('quantity');

        $progress = ($goalCases > 0) ? ($quantityThisWeek / $goalCases) * 100 : 0;

        // Sales trend for chart
        $salesTrendLabels = [];
        $salesTrendData = [];
        $salesTrendQty = [];
        $dailySales = [];

        $currentDate = $currentMonthStart->copy();
        while ($currentDate <= $currentMonthEnd) {
            $label = $currentDate->format('M d');

            $dailyTotal = DivanjSale::where('employee_id', $employee->id)
                ->whereDate('date', $currentDate)
                ->sum('total');

            $dailyQty = DivanjSale::where('employee_id', $employee->id)
                ->whereDate('date', $currentDate)
                ->sum('quantity');

            $salesTrendLabels[] = $label;
            $salesTrendData[] = round($dailyTotal, 2);
            $salesTrendQty[] = $dailyQty;

            $dailySales[] = [
                'date' => $currentDate->format('Y-m-d'),
                'sales_qty' => $dailyQty,
                'sales_amount' => $dailyTotal,
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
            'dailySales',
            'topPerformerFormatted',
            'goalCases',
            'progress'
        ));
    }





}