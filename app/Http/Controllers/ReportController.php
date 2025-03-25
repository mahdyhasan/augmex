<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Carbon\CarbonPeriod;
use DateTimeZone;
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
use App\Models\InvoiceItem;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;

class ReportController extends Controller
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

    // SALES REPORT FOR TEAM AND INDIVIDUAL
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

        return view('reports.sales_report', compact('sales', 'employees', 'clients', 'attendance'));
    }


   
//Sales Summary for Dillon
public function salesSummaryDillon(Request $request)
{
    $clients = Client::all();
    $startDate = $request->input('start_date', now()->toDateString());
    $endDate = $request->input('end_date', now()->toDateString());
    $clientId = $request->input('client_id');

    $employees = Employee::with('user')
        ->where('client_id', $clientId)
        ->get();

    // Get absent status ID
    $absentStatusId = AttendanceStatus::where('name', 'Absent')->first()?->id;

    // Get attendance
    $attendance = Attendance::whereIn('employee_id', $employees->pluck('id'))
        ->whereBetween('date', [$startDate, $endDate])
        ->with('employee.user')
        ->get();

    // Group attendance by date
    $attendanceByDate = [];
    foreach ($attendance as $record) {
        $date = $record->date;

        $checkIn = $record->check_in ? Carbon::parse($record->check_in)->timezone('Australia/Melbourne') : null;
        $checkOut = $record->check_out ? Carbon::parse($record->check_out)->timezone('Australia/Melbourne') : null;

        $hours = 0;
        if ($checkIn && $checkOut) {
            $diff = $checkIn->floatDiffInHours($checkOut);
            $hours = min(round($diff * 2) / 2, 8); // round to nearest 0.5, max 8 hours
        }

        $attendanceByDate[$date][] = [
            'employee' => $record->employee,
            'check_in' => $checkIn?->format('H:i'),
            'check_out' => $checkOut?->format('H:i'),
            'hours' => $hours,
            'status_id' => $record->status_id,
        ];
    }

    // Absent employees by date
    $absentEmployeesByDate = [];
    foreach ($employees as $employee) {
        $attendanceDates = $attendance->where('employee_id', $employee->id)->pluck('date')->toArray();

        $period = Carbon::parse($startDate)->toPeriod($endDate);
        foreach ($period as $day) {
            $d = $day->toDateString();
            if (!in_array($d, $attendanceDates)) {
                $absentEmployeesByDate[$d][] = $employee;
            }
        }
    }

    // Sales records
    $sales = EmployeeSales::whereIn('employee_id', $employees->pluck('id'))
        ->whereBetween('date', [$startDate, $endDate])
        ->with('employee.user')
        ->get();

    // Group sales by date
    $salesByDate = [];
    $totalCases = 0;
    $totalSales = 0;

    foreach ($sales as $sale) {
        $date = $sale->date;
        $cases = round($sale->sales_qty);
        $amount = $sale->sales_amount;

        $salesByDate[$date][] = [
            'employee' => $sale->employee,
            'cases' => $cases,
            'amount' => $amount,
        ];

        $totalCases += $cases;
        $totalSales += $amount;
    }

    return view('reports.sales_summary_dillon', compact(
        'clients', 'employees', 'startDate', 'endDate', 'clientId',
        'attendanceByDate', 'salesByDate', 'absentEmployeesByDate',
        'totalCases', 'totalSales', 'absentStatusId'
    ));
}




public function narrativeReport(Request $request)
{
    $employees = Employee::all();

    $employeeId = $request->get('employee_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
    $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

    $employee = null;
    $sales = collect();
    $lateDays = 0;
    $absentDays = 0;
    $totalSalesQty = 0;
    $totalSalesAmount = 0;
    $salesByWeek = collect();
    $bestWeek = $worstWeek = $bestDay = $worstDay = null;
    $bestWeekAmount = $worstWeekAmount = 0;

    if ($employeeId) {
        $employee = Employee::find($employeeId);

        // Get daily sales
        $sales = EmployeeSales::selectRaw('DATE(date) as date, SUM(sales_qty) as total_qty, SUM(sales_amount) as total_amount')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupByRaw('DATE(date)')
            ->orderBy('date')
            ->get();

        // Attendance analysis
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

    //$lateDays = $attendances->where('login_time', '>', '09:30:00')->count();
    //$absentStatusId = 3; // Adjust as per your DB
    //$absentDays = $attendances->where('status_id', $absentStatusId)->count();

        $lateDays = $attendances->where('isLate', 1)->count();
        $absentStatusId = 2;
        $absentDays = $attendances->where('status_id', $absentStatusId)->count();


        // Totals
        $totalSalesQty = $sales->sum('total_qty');
        $totalSalesAmount = $sales->sum('total_amount');

        // Weekly performance
        $salesByWeek = $sales->groupBy(function ($item) {
            return Carbon::parse($item->date)->startOfWeek()->toDateString();
        })->map(function ($weekSales) {
            return $weekSales->sum('total_amount');
        });

        if ($salesByWeek->isNotEmpty()) {
            $bestWeek = $salesByWeek->sortDesc()->keys()->first();
            $bestWeekAmount = $salesByWeek[$bestWeek] ?? 0;

            $worstWeek = $salesByWeek->sort()->keys()->first();
            $worstWeekAmount = $salesByWeek[$worstWeek] ?? 0;
        }

        // Daily performance
        if ($sales->isNotEmpty()) {
            $bestDay = $sales->sortByDesc('total_amount')->first();
            $worstDay = $sales->sortBy('total_amount')->first();
        }
    }

    return view('reports.narrative_report', compact(
        'employees',
        'employee',
        'startDate',
        'endDate',
        'sales',
        'lateDays',
        'absentDays',
        'totalSalesQty',
        'totalSalesAmount',
        'salesByWeek',
        'bestWeek',
        'bestWeekAmount',
        'worstWeek',
        'worstWeekAmount',
        'bestDay',
        'worstDay'
    ));
    
}




public function commissionforDillon(Request $request)
{
    $clients = DB::table('clients')->get();
    $salesData = [];
    $weeklySummary = (object)[ 'total_qty' => 0 ];

    // ✅ Define default values to avoid "undefined variable" errors
    $week = null;
    $clientId = null;

    if ($request->has('week') && $request->has('client_id')) {
        $clientId = $request->client_id;
        $week = $request->week;

        [$year, $weekNumber] = explode('-W', $week);
        $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate = (clone $startDate)->endOfWeek();

$salesData = DB::table('employee_sales')
    ->join('employees', 'employee_sales.employee_id', '=', 'employees.id')
    ->select(
        'employees.id as employee_id',
        'employees.stage_name',
        DB::raw('SUM(employee_sales.sales_qty) as achieved'),
        DB::raw('SUM(CASE WHEN DAYOFWEEK(employee_sales.date) IN (1, 7) THEN employee_sales.sales_amount ELSE 0 END) as weekend_sales'),
        DB::raw('MIN(employee_sales.date) as start_date'),
        DB::raw('MAX(employee_sales.date) as end_date')
    )
    ->where('employee_sales.client_id', $clientId)
    ->whereBetween('employee_sales.date', [$startDate, $endDate])
    ->groupBy('employees.id', 'employees.stage_name')
    ->havingRaw('SUM(employee_sales.sales_qty) >= 15') // 👈 Add this line
    ->get();

        $weeklySummary = DB::table('employee_sales')
            ->where('client_id', $clientId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw('SUM(sales_qty) as total_qty'))
            ->first();
    }

    return view('reports.commission_dillon', compact(
        'clients', 'salesData', 'weeklySummary', 'week', 'clientId'
    ));
}


    // Update sales data (for Ajax)
    public function updateCommissionForDillon(Request $request)
    {
        DB::table('employee_sales')
            ->where('employee_id', $request->employee_id)
            ->update([
                'target' => $request->target,
                'achieved' => $request->achieved,
                'weekly_commission' => $request->weekly_commission,
                'weekend_sales' => $request->weekend_sales,
            ]);

        return response()->json(['status' => 'success']);
    }
    
    
    
    
    
    
    
    
    
    
    
}