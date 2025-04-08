<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Carbon\CarbonPeriod;
use DateTimeZone;
use DB;
use App\Models\DivanjSale;
use App\Models\DivanjCommission;
use App\Models\Employee;
use App\Models\Attendance;

class PredictionController extends Controller
{
    public function salesPredictionReport(Request $request)
    {
        // Input validation and defaults
        $startDateInput = $request->input('start_date', now()->subMonth()->startOfDay()->toDateString());
        $endDateInput = $request->input('end_date', now()->endOfDay()->toDateString());
        $employeeId = $request->input('employee_id');

        $startDate = Carbon::parse($startDateInput);
        $endDate = Carbon::parse($endDateInput);

        // Validate date range
        if ($startDate->gt($endDate)) {
            return redirect()->back()->withErrors(['date_range' => 'End date must be after start date']);
        }

        // Fetch all employees for selection (admins see all, others see only themselves)
        $employees = Auth::user()->isSuperAdmin()
            ? Employee::with('user')->get()->toArray()
            : [Employee::with('user')->where('id', Auth::user()->employee->id)->first()->toArray()];

        // If no employee is selected, show the selection form
        if (!$employeeId) {
            return view('ai.predictive_sales_report', [
                'employees' => $employees,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ]);
        }

        // Fetch the selected employee
        $employee = Employee::with('user')->findOrFail($employeeId);

        // Get data
        $salesData = $this->getSalesData($employeeId, $startDate, $endDate);
        $performanceSummary = $this->getPerformanceSummary($salesData, $startDate, $endDate);
        $attendance = $this->getAttendance($employeeId, $startDate, $endDate);
        $topCategory = $this->getTopSellingCategory($employeeId, $startDate, $endDate);
        $salesPerformance = $this->getSalesPerformance($salesData);
        $aiInsights = $this->getAIInsights($salesData, $employeeId, $startDate, $endDate);
        $commissionInsights = $this->getCommissionInsights($employeeId, $startDate, $endDate, $aiInsights);
        $chartData = $this->prepareChartData($salesData);

        return view('ai.predictive_sales_report', [
            'employees' => $employees,
            'employee' => $employee->toArray(),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'performanceSummary' => $performanceSummary,
            'attendance' => $attendance,
            'topCategory' => $topCategory,
            'salesPerformance' => $salesPerformance,
            'aiInsights' => $aiInsights,
            'commissionInsights' => $commissionInsights,
            'chartData' => $chartData
        ]);
    }

    private function getSalesData($employeeId, Carbon $startDate, Carbon $endDate)
    {
        return DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getPerformanceSummary($sales, Carbon $startDate, Carbon $endDate)
    {
        $dailySales = [];
        foreach ($sales as $sale) {
            $date = Carbon::parse($sale['date'])->format('Y-m-d');
            if (!isset($dailySales[$date])) {
                $dailySales[$date] = ['date' => $date, 'total_qty' => 0, 'total_amount' => 0];
            }
            $dailySales[$date]['total_qty'] += $sale['quantity'];
            $dailySales[$date]['total_amount'] += $sale['total'];
        }

        $totalCasesSold = array_sum(array_column($dailySales, 'total_qty'));
        $totalRevenue = array_sum(array_column($dailySales, 'total_amount'));

        // Filter out days with 0 sales for best/worst day calculation
        $nonZeroDays = array_filter($dailySales, fn($day) => $day['total_qty'] > 0);

        if (empty($nonZeroDays)) {
            $bestDay = $worstDay = 'No sales data';
            $bestDaySales = $worstDaySales = 0;
        } else {
            usort($nonZeroDays, fn($a, $b) => $b['total_qty'] <=> $a['total_qty']);
            $bestDay = Carbon::parse($nonZeroDays[0]['date'])->format('M d, Y');
            $bestDaySales = $nonZeroDays[0]['total_qty'];
            $worstDay = Carbon::parse(end($nonZeroDays)['date'])->format('M d, Y');
            $worstDaySales = end($nonZeroDays)['total_qty'];
        }

        return [
            'period' => $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'),
            'totalCasesSold' => $totalCasesSold,
            'totalRevenue' => round($totalRevenue, 2),
            'bestDate' => $bestDay,
            'worstDate' => $worstDay,
            'bestDaySales' => $bestDaySales ?? 0,
            'worstDaySales' => $worstDaySales ?? 0
        ];
    }

    private function getAttendance($employeeId, Carbon $startDate, Carbon $endDate)
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->toArray();

        $lateDays = count(array_filter($attendance, fn($day) => $day['isLate'] == 1));
        $absentDays = count(array_filter($attendance, fn($day) => $day['status_id'] == 2));

        return [
            'lateDays' => $lateDays,
            'absentDays' => $absentDays
        ];
    }

    private function getTopSellingCategory($employeeId, Carbon $startDate, Carbon $endDate)
    {
        $wineTypes = [
            'Shiraz', 'Cabernet Sauvignon', 'Pinot Noir', 'Sauvignon Blanc',
            'Merlot', 'Pinot Grigio', 'Malbec', 'Chardonnay', 'Cabernet',
            'Rosé', 'Prosecco', 'Riesling', 'Zinfandel', 'Tempranillo'
        ];

        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->toArray();

        if (empty($sales)) {
            return [
                'cases' => 0,
                'wineType' => 'No data',
                'bestWines' => ['No data', 'No data', 'No data'],
                'expensiveWines' => ['No data', 'No data', 'No data']
            ];
        }

        $categories = [];
        foreach ($sales as $sale) {
            $type = 'Other';
            foreach ($wineTypes as $wine) {
                if (stripos($sale['name'], $wine) !== false) {
                    $type = $wine;
                    break;
                }
            }
            if (!isset($categories[$type])) {
                $categories[$type] = ['total_qty' => 0, 'products' => [], 'prices' => []];
            }
            $categories[$type]['total_qty'] += $sale['quantity'];
            $categories[$type]['products'][$sale['name']] = ($categories[$type]['products'][$sale['name']] ?? 0) + $sale['quantity'];
            $categories[$type]['prices'][$sale['name']] = $sale['price'];
        }

        uasort($categories, fn($a, $b) => $b['total_qty'] <=> $a['total_qty']);
        $topCategory = reset($categories) ?: ['total_qty' => 0, 'products' => [], 'prices' => []];
        $topType = key($categories) ?: 'No data';

        arsort($topCategory['products']);
        $bestWines = array_slice(array_keys($topCategory['products'] + array_fill(0, 3, 'No data')), 0, 3);

        arsort($topCategory['prices']);
        $expensiveWines = array_slice(array_keys($topCategory['prices'] + array_fill(0, 3, 'No data')), 0, 3);

        return [
            'cases' => $topCategory['total_qty'],
            'wineType' => $topType,
            'bestWines' => $bestWines,
            'expensiveWines' => $expensiveWines
        ];
    }

    private function getSalesPerformance($sales)
    {
        $weekdaySales = [
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0,
            'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0
        ];
    
        // Initialize hourly sales with all 24 hours (0-23) as integers
        $hourlySales = array_fill(0, 24, 0);
    
        foreach ($sales as $sale) {
            $day = Carbon::parse($sale['date'])->format('l');
            $hour = (int)Carbon::parse($sale['time'])->format('H'); // Convert to integer
            
            // Ensure hour is within valid range
            $hour = max(0, min(23, $hour));
            
            $weekdaySales[$day] += $sale['quantity'];
            $hourlySales[$hour] += $sale['quantity']; // Now using integer index
        }
    
        // Rest of the method remains the same...
        $weekdaySalesFiltered = array_intersect_key($weekdaySales, array_flip(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']));
        
        arsort($weekdaySalesFiltered);
        $bestDay = key($weekdaySalesFiltered) ?: 'No data';
        $worstDay = array_key_last($weekdaySalesFiltered) ?: 'No data';
    
        arsort($hourlySales);
        $bestHour = key($hourlySales);
        $bestTime = $bestHour !== null ? Carbon::createFromTime($bestHour)->format('g A') : 'No data';
    
        return [
            'bestDay' => $bestDay,
            'worstDay' => $worstDay,
            'bestTime' => $bestTime,
            'weekdaySales' => $weekdaySalesFiltered
        ];
    }
    

    private function getAIInsights($sales, $employeeId, Carbon $startDate, Carbon $endDate)
    {
        if (empty($sales)) {
            return [
                'trend' => 'No data',
                'nextDayPrediction' => 0,
                'nextDayChange' => 0,
                'nextDayTrend' => 'neutral',
                'nextWeekSameDayPrediction' => 0,
                'nextWeekChange' => 0,
                'nextWeekTrend' => 'neutral',
                'peakDays' => ['No data', 'No data'],
                'salesForecast' => 'Not enough data to predict'
            ];
        }

        // Calculate daily sales
        $dailySales = [];
        foreach ($sales as $sale) {
            $date = Carbon::parse($sale['date'])->format('Y-m-d');
            $dailySales[$date] = ($dailySales[$date] ?? 0) + $sale['quantity'];
        }

        // Calculate trend
        $quantities = array_values($dailySales);
        $days = range(1, count($quantities));
        $n = count($quantities);

        $sumX = array_sum($days);
        $sumY = array_sum($quantities);
        $sumXY = array_sum(array_map(fn($x, $y) => $x * $y, $days, $quantities));
        $sumXX = array_sum(array_map(fn($x) => $x * $x, $days));

        $slope = ($n * $sumXY - $sumX * $sumY) / (($n * $sumXX - $sumX * $sumX) ?: 1);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $trend = $slope > 0.5 ? 'Up' : ($slope < -0.5 ? 'Down' : 'Flat');
        $nextDayPrediction = max(0, round($slope * ($n + 1) + $intercept));
        
        // Calculate percentage change from last period
        $lastPeriodAvg = $n > 7 ? array_sum(array_slice($quantities, -7)) / 7 : ($n > 0 ? array_sum($quantities) / $n : 0);
        $nextDayChange = $lastPeriodAvg > 0 ? round(($nextDayPrediction - $lastPeriodAvg) / $lastPeriodAvg * 100) : 0;
        $nextDayTrend = $nextDayChange > 5 ? 'up' : ($nextDayChange < -5 ? 'down' : 'neutral');

        // Weekly pattern
        $weeklyPattern = [];
        foreach ($sales as $sale) {
            $day = Carbon::parse($sale['date'])->format('l');
            $weeklyPattern[$day] = ($weeklyPattern[$day] ?? 0) + $sale['quantity'];
        }
        
        // Fill missing days with 0
        $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($allDays as $day) {
            $weeklyPattern[$day] = $weeklyPattern[$day] ?? 0;
        }
        
        arsort($weeklyPattern);
        $peakDays = array_slice(array_keys($weeklyPattern), 0, 2);

        // Next week same day prediction
        $today = Carbon::today();
        $nextWeekSameDay = $today->copy()->addWeek()->format('l');
        $nextWeekSameDayPrediction = isset($weeklyPattern[$nextWeekSameDay]) 
            ? round($weeklyPattern[$nextWeekSameDay] * ($slope > 0 ? 1.05 : ($slope < 0 ? 0.95 : 1))) 
            : 0;
            
        $nextWeekChange = $weeklyPattern[$nextWeekSameDay] > 0 
            ? round(($nextWeekSameDayPrediction - $weeklyPattern[$nextWeekSameDay]) / $weeklyPattern[$nextWeekSameDay] * 100)
            : 0;
        $nextWeekTrend = $nextWeekChange > 5 ? 'up' : ($nextWeekChange < -5 ? 'down' : 'neutral');

        $forecast = $n > 0 
            ? "We expect $nextDayPrediction cases tomorrow ($nextDayChange% change). " .
              "Next week's same day prediction: $nextWeekSameDayPrediction cases ($nextWeekChange% change)."
            : "Not enough data to generate accurate predictions";

        return [
            'trend' => $trend,
            'nextDayPrediction' => $nextDayPrediction,
            'nextDayChange' => $nextDayChange,
            'nextDayTrend' => $nextDayTrend,
            'nextWeekSameDayPrediction' => $nextWeekSameDayPrediction,
            'nextWeekChange' => $nextWeekChange,
            'nextWeekTrend' => $nextWeekTrend,
            'peakDays' => $peakDays,
            'salesForecast' => $forecast
        ];
    }

    private function getCommissionInsights($employeeId, Carbon $startDate, Carbon $endDate, $aiInsights)
    {
        $commissions = DivanjCommission::where('employee_id', $employeeId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get()
            ->toArray();

        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->toArray();

        $currentWeek = array_sum(array_column($commissions, 'commission_amount'));
        
        if (empty($sales)) {
            return [
                'currentWeek' => round($currentWeek, 2),
                'currentWeekChange' => 0,
                'currentWeekTrend' => 'neutral',
                'nextWeek' => 0,
                'nextWeekChange' => 0,
                'nextWeekTrend' => 'neutral'
            ];
        }

        // Calculate commission rate
        $totalAmount = array_sum(array_column($sales, 'total'));
        $avgCommissionRate = $totalAmount > 0 ? $currentWeek / $totalAmount : 0.1; // Default 10% if no data

        // Calculate percentage change from last period
        $lastPeriodCommissions = DivanjCommission::where('employee_id', $employeeId)
            ->whereBetween('start_date', [$startDate->copy()->subWeek(), $startDate])
            ->sum('commission_amount');
            
        $lastPeriodChange = $lastPeriodCommissions > 0 
            ? round(($currentWeek - $lastPeriodCommissions) / $lastPeriodCommissions * 100)
            : 0;
        $currentWeekTrend = $lastPeriodChange > 5 ? 'up' : ($lastPeriodChange < -5 ? 'down' : 'neutral');

        // Next week prediction
        $nextWeekSales = $aiInsights['nextDayPrediction'] * 7;
        $avgPrice = count($sales) > 0 ? $totalAmount / array_sum(array_column($sales, 'quantity')) : 0;
        $nextWeek = round($nextWeekSales * $avgPrice * $avgCommissionRate, 2);
        
        $nextWeekChange = $currentWeek > 0 ? round(($nextWeek - $currentWeek) / $currentWeek * 100) : 0;
        $nextWeekTrend = $nextWeekChange > 5 ? 'up' : ($nextWeekChange < -5 ? 'down' : 'neutral');

        return [
            'currentWeek' => round($currentWeek, 2),
            'currentWeekChange' => $lastPeriodChange,
            'currentWeekTrend' => $currentWeekTrend,
            'nextWeek' => $nextWeek,
            'nextWeekChange' => $nextWeekChange,
            'nextWeekTrend' => $nextWeekTrend
        ];
    }

    private function prepareChartData($sales)
    {
        $dailySales = [];
        foreach ($sales as $sale) {
            $date = Carbon::parse($sale['date'])->format('M d');
            $dailySales[$date] = ($dailySales[$date] ?? 0) + $sale['quantity'];
        }

        return [
            'labels' => array_keys($dailySales),
            'quantities' => array_values($dailySales)
        ];
    }
}