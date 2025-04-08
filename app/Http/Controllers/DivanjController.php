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
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            // Redirect non-admins to user dashboard
            return redirect()->route('dashboard');
            
            // Or show 403 forbidden
            // abort(403, 'Unauthorized action.');
        }        

         $employees = Employee::where('client_id', 1)->get();
         
         $commissions = DivanjCommission::with('employee')
             ->orderBy('start_date', 'desc')
             ->get();
     
         return view('divanj.commission_index', compact('commissions', 'employees'));
     }
     
     
     

    /**
     * Generates commission for Divanj employees within a date range
     * Processes data week by week to calculate commissions
     */
    public function generateCommissionDivanj(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end   = Carbon::parse($validated['end_date'])->endOfDay();

        $currentStart = $start->copy();
        while ($currentStart <= $end) {
            $currentEnd = $currentStart->copy()->endOfWeek();
            if ($currentEnd > $end) {
                $currentEnd = $end;
            }

            $this->processCommissionForWeek($currentStart, $currentEnd);
            $currentStart = $currentEnd->copy()->addDay()->startOfDay();
        }

        return redirect()
            ->route('divanj.commission.index')
            ->with('success', 'Commission generated successfully.');
    }

    /**
     * Process commission calculations for each employee for a week.
     */
    protected function processCommissionForWeek(Carbon $weekStart, Carbon $weekEnd): void
    {
        Employee::activeForClient(1)
            ->get()
            ->each(function (Employee $employee) use ($weekStart, $weekEnd) {
                $sales = $employee->getFilteredSales($weekStart, $weekEnd);
                $this->processEmployeeCommission($employee, $sales, $weekStart, $weekEnd);
            });
    }

    /**
     * Process commission for a single employee.
     */
    protected function processEmployeeCommission(
        Employee $employee,
        $sales,
        Carbon $weekStart,
        Carbon $weekEnd
    ): void {
        $hiringDate = Carbon::parse($employee->date_of_hire);
        // Use the difference in days between the hiring date and the week start
        $daysWithCompany = $hiringDate->diffInDays($weekStart);
        $target = $daysWithCompany < 60 ? 15 : 25;
    
        // Categorize sales into weekday and weekend totals
        list($weekdayQty, $weekdayAmount, $weekendQty, $weekendAmount) = $this->categorizeSales($sales);
    
        // Pass the days difference along to commission option calculations
        $commissionData = $this->calculateCommissionOptions(
            $weekdayQty,
            $weekdayAmount,
            $weekendQty,
            $weekendAmount,
            $target,
            $employee,
            $weekStart,
            $weekEnd,
            $daysWithCompany
        );
    
        // Save commission record with the best option selected
        $this->saveCommissionRecord($employee, $weekStart, $weekEnd, $target, $commissionData);
    }
     
    

 
     /**
      * Categorize sales into weekday/weekend totals
      */
      protected function calculateTarget(int $monthsWithCompany): int
      {
          return $monthsWithCompany <= 2 ? 15 : 25;
      }
  
    /**
     * Categorize sales into weekday and weekend totals.
     */
    protected function categorizeSales($sales): array
    {
        $weekdaySales = $sales->filter(fn($sale) => Carbon::parse($sale->date)->isWeekday());
        $weekendSales = $sales->filter(fn($sale) => !Carbon::parse($sale->date)->isWeekday());

        return [
            $weekdaySales->sum('quantity'),
            $weekdaySales->sum('total'),
            $weekendSales->sum('quantity'),
            $weekendSales->sum('total'),
        ];
    }      
      


    /**
     * Calculate commission options and include the days difference for commission slab selection.
     */
    protected function calculateCommissionOptions(
        int $weekdayQty,
        float $weekdayAmount,
        int $weekendQty,
        float $weekendAmount,
        int $target,
        Employee $employee,
        Carbon $weekStart,
        Carbon $weekEnd,
        int $daysWithCompany
    ): array {
        $totalQty    = $weekdayQty + $weekendQty;
        $totalAmount = $weekdayAmount + $weekendAmount;

        return [
            'base'           => $this->calculateBaseCommission($totalQty, $totalAmount, $daysWithCompany),
            'option_a'       => $this->calculateOptionA($weekdayQty, $weekdayAmount, $weekendAmount, $target, $daysWithCompany),
            'option_b'       => $this->calculateOptionB(
                                    $weekdayQty,
                                    $weekdayAmount,
                                    $weekendQty,
                                    $weekendAmount,
                                    $target,
                                    $employee,
                                    $weekStart,
                                    $weekEnd,
                                    $daysWithCompany
                                ),
            'option_c'       => $this->calculateOptionC($weekendAmount),
            'total_qty'      => $totalQty,
            'total_amount'   => $totalAmount,
            'weekday_qty'    => $weekdayQty,
            'weekday_amount' => $weekdayAmount,
            'weekend_qty'    => $weekendQty,
            'weekend_amount' => $weekendAmount,
        ];
    }    
    
    /**
     * Calculate the base commission using commission slabs based on days with company.
     */
    protected function calculateBaseCommission(int $totalQty, float $totalAmount, int $daysWithCompany): float
    {
        $slabs = $this->getCommissionSlabs($daysWithCompany);

        foreach ($slabs as [$min, $max, $fixed, $percent]) {
            if ($totalQty >= $min && (is_null($max) || $totalQty <= $max)) {
                $calculated = $percent ? max($fixed, $totalAmount * $percent) : $fixed;
                return $totalQty >= $slabs[0][0] ? $calculated : 0;
            }
        }

        return 0;
    }
    

    /**
     * Get commission slab configuration based on days with company.
     * If less than 60 days, use the lower target and corresponding slabs.
     */
    protected function getCommissionSlabs(int $daysWithCompany): array
    {
        if ($daysWithCompany < 60) {
            return [
                [15, 34, 50, null],
                [35, 49, 75, null],
                [50, 59, 100, 0.03],
                [60, null, 125, 0.04],
            ];
        } else {
            return [
                [25, 34, 50, null],
                [35, 49, 75, null],
                [50, 59, 100, 0.03],
                [60, null, 125, 0.04],
            ];
        }
    }

        
    /**
     * Option A (Fixed): If weekday sales meet the target, add 6% of all weekend sales.
     */
    protected function calculateOptionA(
        int $weekdayQty,
        float $weekdayAmount,
        float $weekendAmount,
        int $target,
        int $daysWithCompany
    ): float {
        if ($weekdayQty >= $target) {
            $base = $this->calculateBaseCommission($weekdayQty, $weekdayAmount, $daysWithCompany);
            return $base + ($weekendAmount * 0.06);
        }
        return 0.0;
    }

    /**
     * Option B (Mixed): Use a portion of weekend sales to meet the target, then commission the surplus at 6%.
     */
    protected function calculateOptionB(
        int $weekdayQty,
        float $weekdayAmount,
        int $weekendQty,
        float $weekendAmount,
        int $target,
        Employee $employee,
        Carbon $weekStart,
        Carbon $weekEnd,
        int $daysWithCompany
    ): float {
        // Ensure total sales (weekday + weekend) meet the target
        if (($weekdayQty + $weekendQty) < $target) return 0.0;
        
        $needed = max(0, $target - $weekdayQty);
        // Retrieve weekend sales sorted in ascending order by total amount
        $weekendSales = $this->getSortedWeekendSales($employee, $weekStart, $weekEnd);
        
        // Calculate how much weekend sale amount is used to reach the target and the surplus amount
        list($usedAmount, $remainingAmount) = $this->calculateUsedWeekendSales($weekendSales, $needed);

        // Combine weekday sales amount with the used weekend sales amount for base commission
        $combinedAmount = $weekdayAmount + $usedAmount;
        $base = $this->calculateBaseCommission($target, $combinedAmount, $daysWithCompany);
        
        return $base + ($remainingAmount * 0.06);
    }


    /**
     * Option C (Weekend Only): 6% commission on all weekend sales.
     */
    protected function calculateOptionC(float $weekendAmount): float
    {
        return $weekendAmount * 0.06;
    }

 
    /**
     * Retrieve sorted weekend sales (ascending by total).
     */
    protected function getSortedWeekendSales(Employee $employee, Carbon $start, Carbon $end)
    {
        return DivanjSale::where('employee_id', $employee->id)
            ->betweenDates($start, $end)
            ->weekend()
            ->orderBy('total')
            ->get();
    }


    /**
     * Calculate used and remaining weekend sales amounts based on needed units.
     * (Assumes each sale record represents one unit; adjust if needed.)
     */
    protected function calculateUsedWeekendSales($weekendSales, int $needed): array
    {
        $usedAmount = 0;
        $remainingAmount = 0;
        $casesCounted = 0;

        foreach ($weekendSales as $sale) {
            if ($casesCounted < $needed) {
                $usedAmount += $sale->total;
                $casesCounted++;
            } else {
                $remainingAmount += $sale->total;
            }
        }

        return [$usedAmount, $remainingAmount];
    }





    /**
     * Determine the best commission option.
     */
    protected function determineBestOption(
        float $optionA,
        float $optionB,
        float $optionC,
        int $weekdayQty,
        int $target
    ): array {
        $options = [
            'fixed'   => $weekdayQty >= $target ? $optionA : 0,
            'mixed'   => $weekdayQty < $target ? $optionB : 0,
            'weekend' => $optionC,
        ];

        $maxType = array_keys($options, max($options))[0];
        return [
            'type'   => $maxType,
            'amount' => $options[$maxType],
        ];
    }

    /**
     * Save commission record to the database.
     */
    protected function saveCommissionRecord(
        Employee $employee,
        Carbon $weekStart,
        Carbon $weekEnd,
        int $target,
        array $commissionData
    ) {
        $bestOption = $this->determineBestOption(
            $commissionData['option_a'],
            $commissionData['option_b'],
            $commissionData['option_c'],
            $commissionData['weekday_qty'],
            $target
        );

        DivanjCommission::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'start_date'  => $weekStart->toDateString(),
                'end_date'    => $weekEnd->toDateString(),
            ],
            [
                'target'                => $target,
                'achieved_qty'          => $commissionData['total_qty'],
                'weekday_sales_qty'     => $commissionData['weekday_qty'],
                'weekday_sales_amount'  => $commissionData['weekday_amount'],
                'weekend_sales_qty'     => $commissionData['weekend_qty'],
                'weekend_sales_amount'  => $commissionData['weekend_amount'],
                'base_commission'       => $commissionData['base'],
                'option_a_amount'       => $commissionData['option_a'],
                'option_b_amount'       => $commissionData['option_b'],
                'option_c_amount'       => $commissionData['option_c'],
                'commission_type'       => $bestOption['type'],
                'commission_amount'     => $bestOption['amount'],
            ]
        );
    }

     
    
    
        // FOR AGENT 
        public function salesCommissionForAgent()
        {
            // Retrieve the employee record for the logged-in user.
            $employee = auth()->user()->employee;
            
            // Get commissions for the authenticated employee who belongs to client_id = 1.
            $commissions = DivanjCommission::whereHas('employee', function($query) {
                                    $query->where('client_id', 1);
                                })
                                ->where('employee_id', $employee->id)
                                ->orderBy('start_date', 'desc')
                                ->get();
        
            return view('divanj.commission_table', compact('commissions'));
        }
        

    
    public function salesSummaryDivanj(Request $request)
    {
        // Ensure only super admins can access this functionality.
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            // Redirect non-admin users to the dashboard.
            return redirect()->route('dashboard');
            // Alternatively, you can abort with a 403 status:
            // abort(403, 'Unauthorized action.');
        }

        // Retrieve the start and end dates from the request, defaulting to today.
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        // Fetch active employees for client with ID 1.
        $employees = Employee::with('user')
            ->where('client_id', 1)
            ->whereNull('date_of_termination')
            ->get();

        // Get the ID for the "Absent" status.
        $absentStatusId = AttendanceStatus::where('name', 'Absent')->first()?->id ?? null;

        // Retrieve attendance records for the selected employees within the date range.
        $attendance = Attendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee.user')
            ->get();

        // Process and group attendance records by date.
        $attendanceByDate = [];
        foreach ($attendance as $record) {
            $dateString = Carbon::parse($record->date)->format('Y-m-d');

            $checkIn = $record->check_in
                ? Carbon::parse($record->check_in)->timezone('Australia/Melbourne')
                : null;
            $checkOut = $record->check_out
                ? Carbon::parse($record->check_out)->timezone('Australia/Melbourne')
                : null;

            $hours = 0;
            if ($checkIn && $checkOut) {
                $diff = $checkIn->floatDiffInHours($checkOut);
                $hours = min(round($diff * 2) / 2, 8); // Round to the nearest 0.5 hour, capped at 8 hours.
            }

            $attendanceByDate[$dateString][] = [
                'employee'  => $record->employee,
                'check_in'  => $checkIn ? $checkIn->format('H:i') : null,
                'check_out' => $checkOut ? $checkOut->format('H:i') : null,
                'hours'     => $hours,
                'status_id' => $record->status_id,
            ];
        }

        // Identify absent employees for each day in the period.
        $absentEmployeesByDate = [];
        foreach ($employees as $employee) {
            // Gather all attendance dates for this employee.
            $attendanceDates = $attendance->where('employee_id', $employee->id)
                ->pluck('date')
                ->map(fn($date) => Carbon::parse($date)->toDateString())
                ->toArray();

            // Check each day within the range.
            $period = Carbon::parse($startDate)->toPeriod($endDate);
            foreach ($period as $day) {
                $dateStr = $day->toDateString();
                if (!in_array($dateStr, $attendanceDates)) {
                    $absentEmployeesByDate[$dateStr][] = $employee;
                }
            }
        }

        // Retrieve sales records for the employees within the date range.
        $sales = DivanjSale::whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee.user')
            ->get();

        // Process and group sales records by date and employee.
        $salesByDate = [];
        $totalCases  = 0;
        $totalSales  = 0;
        foreach ($sales as $sale) {
            $dateString = Carbon::parse($sale->date)->format('Y-m-d');

            // Initialize the date group if not already set.
            if (!isset($salesByDate[$dateString])) {
                $salesByDate[$dateString] = [];
            }

            $employeeId = $sale->employee_id;
            // Initialize the employee group for this date if needed.
            if (!isset($salesByDate[$dateString][$employeeId])) {
                $salesByDate[$dateString][$employeeId] = [
                    'employee' => $sale->employee,
                    'cases'    => 0,
                    'amount'   => 0,
                ];
            }

            // Accumulate the sales data.
            $salesByDate[$dateString][$employeeId]['cases']  += $sale->quantity;
            $salesByDate[$dateString][$employeeId]['amount'] += $sale->total;
            $totalCases  += $sale->quantity;
            $totalSales  += $sale->total;
        }
        
        
            // Generate employee summaries
            $employeeSummaries = $employees->map(function ($employee) use ($attendanceByDate, $salesByDate, $absentEmployeesByDate, $absentStatusId) {  
                $employeeId = $employee->id;
                $presentDays = 0;
                $absentDays = 0;
                $incompleteDays = 0;
                $totalHoursWorked = 0.0; // Changed from 8 hours per day to cumulative hours calculation
                $totalCases = 0;
                $totalSales = 0;
            
                foreach ($attendanceByDate as $date => $records) {
                    // Check if the current date is a weekend (Saturday or Sunday)
                    $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                    $isWeekend = ($dayOfWeek === \Carbon\Carbon::SATURDAY || $dayOfWeek === \Carbon\Carbon::SUNDAY);
            
                    $employeeRecord = collect($records)->firstWhere('employee.id', $employeeId);
            
                    if (!$isWeekend) { // Only count attendance on weekdays
                        if ($employeeRecord) {
                            if (isset($employeeRecord['status_id']) && $employeeRecord['status_id'] == $absentStatusId) {
                                $absentDays++;
                            } elseif ($employeeRecord['check_in'] && $employeeRecord['check_out']) {
                                $presentDays++;
                                $totalHoursWorked += $employeeRecord['hours']; // Sum actual hours worked for that day
                            } else {
                                $incompleteDays++;
                            }
                        } else {
                            $absentDays++;
                        }
                    }
            
                    // Always count sales, even on weekends
                    if (isset($salesByDate[$date][$employeeId])) {
                        $totalCases += $salesByDate[$date][$employeeId]['cases'];
                        $totalSales += $salesByDate[$date][$employeeId]['amount'];
                    }
                }
            
                return (object)[
                    'stage_name'     => $employee->stage_name,
                    'present_days'   => $presentDays,
                    'absent_days'    => $absentDays,
                    'incomplete_days'=> $incompleteDays,
                    'total_hours_worked' => $totalHoursWorked, // Store the actual total hours worked
                    'total_cases'    => $totalCases,
                    'total_sales'    => $totalSales,
                ];
            });


        
        // Pass all data to the view.
        return view('divanj.sales_summary', compact(
            'employees',
            'startDate',
            'endDate',
            'attendanceByDate',
            'salesByDate',
            'absentEmployeesByDate',
            'totalCases',
            'totalSales',
            'absentStatusId',
            'employeeSummaries' // This is the new variable passed to the view
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
    






    public function divanjDashboard()
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            // Redirect non-admins to user dashboard
            return redirect()->route('dashboard');
            
            // Or show 403 forbidden
            // abort(403, 'Unauthorized action.');
        }        

        // Basic counts
        $employeeCount = Employee::where('client_id', 1)->count();
        
        // Sales totals
        $salesTotals = DivanjSale::selectRaw(
            'SUM(quantity) as total_units,
            SUM(total) as total_amount'
        )->first();
        
        // Commission totals
        $commissionTotals = DivanjCommission::selectRaw(
            'SUM(commission_amount) as total_commission'
        )->first();
        
        // Weekly sales data for chart
        $weeklySales = DivanjSale::selectRaw(
            'YEAR(date) as year, 
            WEEK(date) as week,
            SUM(quantity) as units,
            SUM(total) as amount'
        )
        ->groupBy('year', 'week')
        ->orderBy('year', 'desc')
        ->orderBy('week', 'desc')
        ->limit(12)
        ->get();
        
        // Recent commissions
        $recentCommissions = DivanjCommission::with('employee')
            ->orderBy('end_date', 'desc')
            ->limit(5)
            ->get();
        
        // Top performers
        $topPerformers = Employee::select('employees.id', 'employees.stage_name')
            ->join('divanj_commissions', 'employees.id', '=', 'divanj_commissions.employee_id')
            ->selectRaw('SUM(divanj_commissions.commission_amount) as total_commission')
            ->groupBy('employees.id', 'employees.stage_name')
            ->orderBy('total_commission', 'desc')
            ->limit(5)
            ->get();
        
        return view('divanj.dashboard', compact(
            'employeeCount',
            'salesTotals',
            'commissionTotals',
            'weeklySales',
            'recentCommissions',
            'topPerformers'
        ));
    }














    

}
