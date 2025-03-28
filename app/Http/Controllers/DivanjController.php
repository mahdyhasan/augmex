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
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

use App\Models\User;
use App\Models\UserType;

use App\Imports\DivanjSalesImport;


class DivanjController extends Controller
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


     public function showCommissionListDivanj()
     {
         $commissions = DivanjCommission::with('employee')
             ->orderBy('start_date', 'desc')
             ->get();
     
         return view('divanj.commission_index', compact('commissions'));
     }
     
     
     
     
         // COMMISISON FOR DIVANJ
     
         public function generateDivanjCommission(Request $request)
         {   
             $request->validate([
                 'start_date' => 'required|date',
                 'end_date'   => 'required|date|after_or_equal:start_date',
             ]);
     
             $start = Carbon::parse($request->start_date)->startOfDay();
             $end   = Carbon::parse($request->end_date)->endOfDay();
     
             $employees = Employee::all();
     
             foreach ($employees as $employee) {
                 $hiring_date = Carbon::parse($employee->hiring_date);
                 $months_with_company = $hiring_date->diffInMonths($start);
                 $target = $months_with_company <= 2 ? 15 : 25;
             
                 $sales = EmployeeSales::where('employee_id', $employee->id)
                     ->whereBetween('date', [$start, $end])
                     ->get();
             
                 $weekday_qty = 0;
                 $weekday_amount = 0;
                 $weekend_qty = 0;
                 $weekend_amount = 0;
             
                 foreach ($sales as $sale) {
                     $day = Carbon::parse($sale->sale_date)->format('N');
                     if ($day <= 5) {
                         $weekday_qty += $sale->sales_qty;
                         $weekday_amount += $sale->sales_amount;
                     } else {
                         $weekend_qty += $sale->sales_qty;
                         $weekend_amount += $sale->sales_amount;
                     }
                 }
             
                 $achieved_qty = $weekday_qty + $weekend_qty;
             
                 // ---- Option A: Base + Extra Weekend Bonus ----
                 $optionA = 0;
                 if ($achieved_qty >= $target) {
                     $optionA += 50;
             
                     $extra_units = $achieved_qty - $target;
             
                     if ($extra_units > 0 && $weekend_qty > 0) {
                         $weekend_sales = $sales->filter(function ($s) {
                             return Carbon::parse($s->sale_date)->isWeekend();
                         });
             
                         $weekend_units_collected = 0;
                         $extra_units_value = 0;
             
                         foreach ($weekend_sales as $sale) {
                             if ($weekend_units_collected >= $extra_units) break;
             
                             $units_to_add = min($extra_units - $weekend_units_collected, $sale->sales_qty);
                             $unit_price = $sale->sales_amount / $sale->sales_qty;
             
                             $extra_units_value += $units_to_add * $unit_price;
                             $weekend_units_collected += $units_to_add;
                         }
             
                         $optionA += round($extra_units_value * 0.06, 2);
                     }
                 }
             
                 // ---- Option B: 6% of all weekend sales ----
                 $optionB = round($weekend_amount * 0.06, 2);
             
                 // ---- Final Commission ----
                 $commission_options = [
                     'mixed' => $optionA,
                     'weekend' => $optionB
                 ];
             
                 $max_type = array_keys($commission_options, max($commission_options))[0];
                 $final_commission = $commission_options[$max_type];
             
     
                 // Save to DB
                 DivanjCommission::updateOrCreate(
                     [
                         'employee_id' => $employee->id,
                         'start_date'  => $start->toDateString(),
                         'end_date'    => $end->toDateString(),
                     ],
                     [
                         'target'               => $target,
                         'achieved_qty'         => $achieved_qty,
                         'weekday_sales_qty'    => $weekday_qty,
                         'weekday_sales_amount' => $weekday_amount,
                         'weekend_sales_qty'    => $weekend_qty,
                         'weekend_sales_amount' => $weekend_amount,
                         'commission_type'      => $max_type,
                         'commission_amount'    => $final_commission,
                     ]
                 );
             }
     
             return redirect()->route('divanj.commission.index')->with('success', 'Commission generated successfully.');
         }
         
         
         public function editDivanjCommission($id)
         {
             $commission = DivanjCommission::with('employee')->findOrFail($id);
             return view('divanj.commission_edit', compact('commission'));
         }
             
         
         public function updateDivanjCommission(Request $request, $id)
         {
             $request->validate([
                 'commission_amount' => 'required|numeric|min:0',
                 'commission_type'   => 'required|string|in:fixed,mixed,weekend',
             ]);
     
             $commission = DivanjCommission::findOrFail($id);
     
             $commission->commission_amount = $request->commission_amount;
             $commission->commission_type = $request->commission_type;
             $commission->save();
     
             return redirect()->route('divanj.commission.index')->with('success', 'Commission updated successfully.');
         }
     
    
         


    
    //Sales Summary for Dillon
    public function salesSummaryDivanj (Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $employees = Employee::with('user')
            ->where('client_id', 1)
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

        // Sales records from DivanjSale table
        $sales = DivanjSale::whereIn('employee_id', $employees->pluck('id'))
        ->whereBetween('date', [$startDate, $endDate])
        ->with('employee.user')
        ->get();


        // Group sales by date
        $salesByDate = [];
        $totalCases = 0;
        $totalSales = 0;

        foreach ($sales->groupBy(['date', 'employee_id']) as $date => $groupedEmployees) {
            foreach ($groupedEmployees as $employeeId => $salesGroup) {
                $employee = $salesGroup->first()->employee;
                $cases = $salesGroup->sum('quantity');
                $amount = $salesGroup->sum('total');
        
                $salesByDate[$date][] = [
                    'employee' => $employee,
                    'cases' => $cases,
                    'amount' => $amount,
                ];
        
                $totalCases += $cases;
                $totalSales += $amount;
            }
        }

        return view('divanj.sales_summary', compact(
             'employees', 'startDate', 'endDate',
            'attendanceByDate', 'salesByDate', 'absentEmployeesByDate',
            'totalCases', 'totalSales', 'absentStatusId'
        ));
    }




    // IMPORT SALES REPORT    
    public function importSalesDivanj(Request $request)
    {

       $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $employeeId = Auth::user()->employee->id;

        $import = new DivanjSalesImport($employeeId);

        Excel::import($import, $request->file('file'));

        return redirect()->back()->with('success', 'Excel data imported successfully!');
    }


    public function salesReport(Request $request)
    {
        // Access control: allow only Admins and users whose employee->client_id equals 1
        if (!(Auth::user()->isSuperAdmin() || (Auth::user()->employee && Auth::user()->employee->client_id == 1))) {
            abort(403, 'Unauthorized access');
        }
    
        // Fetch employees for the filter dropdown (only relevant ones)
        $employees = Auth::user()->isSuperAdmin() 
            ? Employee::all() 
            : Employee::where('id', Auth::user()->employee->id)->get();
    
        // Build the query using the Sale model
        $salesQuery = DivanjSale::with('employee');
    
        // Apply filters based on request inputs
        if ($request->filled('start_date')) {
            $salesQuery->where('date', '>=', $request->start_date);
        }
    
        if ($request->filled('end_date')) {
            $salesQuery->where('date', '<=', $request->end_date);
        }
    
        // For non-superadmins, restrict to their own data
        if (!Auth::user()->isSuperAdmin()) {
            $salesQuery->where('employee_id', Auth::user()->employee->id);
        } elseif ($request->filled('employee_id')) {
            // Only apply employee filter for superadmins
            $salesQuery->where('employee_id', $request->employee_id);
        }
    
        // Ensure that the sales records have an associated employee
        $sales = $salesQuery->whereHas('employee')->get();
    
        return view('divanj.sales_report', [
            'sales'     => $sales,
            'employees' => $employees,
        ]);
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
    
        return view('divanj.narrative_report', compact(
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
    
    










}
