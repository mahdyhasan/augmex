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








public function commissionforDillon(Request $request)
{
    $clients = DB::table('clients')->get();
    $salesData = [];
    $weeklySummary = (object)[ 'total_qty' => 0 ];

    // âœ… Define default values to avoid "undefined variable" errors
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
    ->havingRaw('SUM(employee_sales.sales_qty) >= 15') // ðŸ‘ˆ Add this line
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