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



    // INDIVIDUAL NARRATIVE REPORT
    public function narrativeReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $employeeId = $request->input('employee_id');
    
        $employees = Auth::user()->isSuperAdmin()
            ? Employee::with('user')->get()
            : Employee::where('id', Auth::user()->employee->id)->with('user')->get();
    
        if (!$employeeId) {
            return view('divanj.narrative_report', compact('employees', 'startDate', 'endDate'));
        }
    
        $employee = Employee::findOrFail($employeeId);
    
        // Daily sales summary
        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(quantity) as total_qty, SUM(total) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    
        $totalSalesQty = $sales->sum('total_qty');
        $totalSalesAmount = $sales->sum('total_amount');
    
        $bestDay = $sales->sortByDesc('total_qty')->first();
        $worstDay = $sales->sortBy('total_qty')->first();
    
        // Weekly sales (based on ISO week)
        $weeklySales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEARWEEK(date, 1) as week, SUM(quantity) as total_qty')
            ->groupBy('week')
            ->get();
    
        $bestWeek = $weeklySales->sortByDesc('total_qty')->first();
        $worstWeek = $weeklySales->sortBy('total_qty')->first();
    
        // Convert raw week to a proper start date string
        $bestWeekStart = $bestWeek
            ? Carbon::now()->setISODate(substr($bestWeek->week, 0, 4), substr($bestWeek->week, 4))->startOfWeek()->toDateString()
            : null;
        $worstWeekStart = $worstWeek
            ? Carbon::now()->setISODate(substr($worstWeek->week, 0, 4), substr($worstWeek->week, 4))->startOfWeek()->toDateString()
            : null;
    
        $bestWeekQty = $bestWeek->total_qty ?? 0;
        $worstWeekQty = $worstWeek->total_qty ?? 0;
    
        // Attendance data
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    
        $lateDays = $attendance->where('isLate', 1)->count();
        $absentDays = $attendance->where('status_id', 2)->count();
    
        // Day-of-week performance (weekdays only)
        $dayOfWeekStats = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereRaw("WEEKDAY(date) < 5")  // Only Monday (0) to Friday (4)
            ->get()
            ->groupBy(function ($sale) {
                return Carbon::parse($sale->date)->format('l'); // e.g., Monday
            })
            ->map(function ($group) {
                return [
                    'qty' => $group->sum('quantity'),
                    'amount' => $group->sum('total'),
                ];
            });
    
        $bestDayOfWeek = $dayOfWeekStats->sortByDesc('qty')->keys()->first();
        $worstDayOfWeek = $dayOfWeekStats->sortBy('qty')->keys()->first();
    
        // Hourly performance (weekdays only)
        $hourlyStats = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereRaw("WEEKDAY(date) < 5")
            ->get()
            ->groupBy(function ($sale) {
                return Carbon::parse($sale->time)->format('H'); // e.g., "13"
            })
            ->map(function ($group) {
                return [
                    'qty' => $group->sum('quantity'),
                    'amount' => $group->sum('total'),
                ];
            });
    
        $bestHour = $hourlyStats->sortByDesc('qty')->keys()->first();
        $worstHour = $hourlyStats->sortBy('qty')->keys()->first();
    
        $bestHourFormatted = $bestHour ? Carbon::createFromTime($bestHour)->format('g A') : null;
        $worstHourFormatted = $worstHour ? Carbon::createFromTime($worstHour)->format('g A') : null;
    
        // Most sold item
        // $mostSoldItem = DivanjSale::where('employee_id', $employeeId)
        //     ->whereBetween('date', [$startDate, $endDate])
        //     ->selectRaw('name, SUM(quantity) as total_qty')
        //     ->groupBy('name')
        //     ->orderByDesc('total_qty')
        //     ->first();

        $wineTypes = [
            'Shiraz', 'Cabernet Sauvignon', 'Pinot Noir', 'Sauvignon Blanc', 
            'Merlot', 'Pinot Grigio', 'Malbec', 'Chardonnay', 'Cabernet',
            'Rosé', 'Prosecco', 'Riesling', 'Zinfandel', 'Tempranillo'
        ];
        
        $mostSoldItems = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) use ($wineTypes) {
                foreach ($wineTypes as $wine) {
                    if (stripos($item->name, $wine) !== false) {
                        return $wine;
                    }
                }
                return 'Other';
            })
            ->map(function ($group, $wineType) {
                return [
                    'wine_type' => $wineType,
                    'total_qty' => $group->sum('quantity'),
                    'examples' => $group->pluck('name')->unique()->take(3)->all() // Convert to array
                ];
            })
            ->sortByDesc('total_qty');
        
        $topWineType = $mostSoldItems->first();
        
        // Alternative: If you want the raw product name with pattern matching
        $mostSoldProduct = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('name, SUM(quantity) as total_qty')
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->get()
            ->first(function ($item) use ($wineTypes) {
                foreach ($wineTypes as $wine) {
                    if (stripos($item->name, $wine) !== false) {
                        return true;
                    }
                }
                return false;
            });
            
            

        return view('divanj.narrative_report', compact(
            'employees',
            'employee',
            'startDate',
            'endDate',
            'sales',
            'totalSalesQty',
            'totalSalesAmount',
            'bestDay',
            'worstDay',
            'bestWeek',
            'worstWeek',
            'bestWeekStart',
            'worstWeekStart',
            'bestWeekQty',
            'worstWeekQty',
            'lateDays',
            'absentDays',
            'bestDayOfWeek',
            'worstDayOfWeek',
            'bestHourFormatted',
            'worstHourFormatted',
            'mostSoldItems',
            'topWineType', 
            'mostSoldProduct'
        ));
    }
                
    
    // NARRATIVE REPORT FOR ALL AGENTS
    public function narrativeReportForAll(Request $request)
    {

        // Access control: allow only Admins
        if (!(Auth::user()->isSuperAdmin() )) {
            abort(403, 'Unauthorized access');
        }

                
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
    
        // All employees for reference or dropdowns
        $employees = Employee::with('user')->get();
    
        // Sales summary (daily)
        $sales = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(quantity) as total_qty, SUM(total) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    
        $totalSalesQty = $sales->sum('total_qty');
        $totalSalesAmount = $sales->sum('total_amount');
    
        $bestDay = $sales->sortByDesc('total_qty')->first();
        $worstDay = $sales->sortBy('total_qty')->first();
    
        // Weekly performance
        $weeklySales = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEARWEEK(date, 1) as week, SUM(quantity) as total_qty')
            ->groupBy('week')
            ->get();
    
        $bestWeek = $weeklySales->sortByDesc('total_qty')->first();
        $worstWeek = $weeklySales->sortBy('total_qty')->first();
    
        $bestWeekStart = $bestWeek
            ? Carbon::now()->setISODate(substr($bestWeek->week, 0, 4), substr($bestWeek->week, 4))->startOfWeek()->toDateString()
            : null;
        $worstWeekStart = $worstWeek
            ? Carbon::now()->setISODate(substr($worstWeek->week, 0, 4), substr($worstWeek->week, 4))->startOfWeek()->toDateString()
            : null;
    
        $bestWeekQty = $bestWeek->total_qty ?? 0;
        $worstWeekQty = $worstWeek->total_qty ?? 0;
    
        // Attendance summary across all employees
        $attendance = Attendance::whereBetween('date', [$startDate, $endDate])->get();
        $lateDays = $attendance->where('isLate', 1)->count();
        $absentDays = $attendance->where('status_id', 2)->count();
    
        // Best/worst weekday (exclude Saturday & Sunday)
        $dayOfWeekStats = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->whereRaw("WEEKDAY(date) < 5")
            ->get()
            ->groupBy(function ($sale) {
                return Carbon::parse($sale->date)->format('l');
            })
            ->map(fn($group) => [
                'qty' => $group->sum('quantity'),
                'amount' => $group->sum('total'),
            ]);
    
        $bestDayOfWeek = $dayOfWeekStats->sortByDesc('qty')->keys()->first();
        $worstDayOfWeek = $dayOfWeekStats->sortBy('qty')->keys()->first();
    
        // Best/worst hour (weekdays only)
        $hourlyStats = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->whereRaw("WEEKDAY(date) < 5")
            ->get()
            ->groupBy(function ($sale) {
                return Carbon::parse($sale->time)->format('H');
            })
            ->map(fn($group) => [
                'qty' => $group->sum('quantity'),
                'amount' => $group->sum('total'),
            ]);
    
        $bestHour = $hourlyStats->sortByDesc('qty')->keys()->first();
        $worstHour = $hourlyStats->sortBy('qty')->keys()->first();
    
        $bestHourFormatted = $bestHour ? Carbon::createFromTime($bestHour)->format('g A') : null;
        $worstHourFormatted = $worstHour ? Carbon::createFromTime($worstHour)->format('g A') : null;
    
        // Wine Type Grouping
        $wineTypes = [
            'Shiraz', 'Cabernet Sauvignon', 'Pinot Noir', 'Sauvignon Blanc', 
            'Merlot', 'Pinot Grigio', 'Malbec', 'Chardonnay', 'Cabernet',
            'Rosé', 'Prosecco', 'Riesling', 'Zinfandel', 'Tempranillo'
        ];
    
        $mostSoldItems = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) use ($wineTypes) {
                foreach ($wineTypes as $wine) {
                    if (stripos($item->name, $wine) !== false) {
                        return $wine;
                    }
                }
                return 'Other';
            })
            ->map(function ($group, $wineType) {
                return [
                    'wine_type' => $wineType,
                    'total_qty' => $group->sum('quantity'),
                    'examples' => $group->pluck('name')->unique()->take(3)->all()
                ];
            })
            ->sortByDesc('total_qty');
    
        $topWineType = $mostSoldItems->first();
    
        $mostSoldProduct = DivanjSale::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('name, SUM(quantity) as total_qty')
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->get()
            ->first(function ($item) use ($wineTypes) {
                foreach ($wineTypes as $wine) {
                    if (stripos($item->name, $wine) !== false) {
                        return true;
                    }
                }
                return false;
            });
    
        return view('divanj.narrative_report_all', compact(
            'employees',
            'startDate',
            'endDate',
            'sales',
            'totalSalesQty',
            'totalSalesAmount',
            'bestDay',
            'worstDay',
            'bestWeek',
            'worstWeek',
            'bestWeekStart',
            'worstWeekStart',
            'bestWeekQty',
            'worstWeekQty',
            'lateDays',
            'absentDays',
            'bestDayOfWeek',
            'worstDayOfWeek',
            'bestHourFormatted',
            'worstHourFormatted',
            'mostSoldItems',
            'topWineType',
            'mostSoldProduct'
        ));
    }
    









}
