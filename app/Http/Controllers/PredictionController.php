<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Models\DivanjSale;
use App\Models\Employee;

class PredictionController extends Controller
{
    public function salesPredictionReport(Request $request)
    {
        // Input validation
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $employeeId = $request->input('employee_id');

        // Fetch employees based on user role
        $employees = Auth::user()->isSuperAdmin()
            ? Employee::with('user')->get()
            : [Employee::with('user')->where('id', Auth::user()->employee->id)->first()];

        // Show employee selection form if no employee is selected
        if (!$employeeId) {
            return view('ai.predictive_sales_report', [
                'employees' => $employees,
            ]);
        }

        // Fetch the selected employee
        $employee = Employee::with('user')->findOrFail($employeeId);

        // Get last available date from sales data
        $lastSaleDate = DivanjSale::where('employee_id', $employeeId)
            ->max('date');

        if (!$lastSaleDate) {
            return view('ai.predictive_sales_report', [
                'employees' => $employees,
                'error' => 'No sales data found for this employee'
            ]);
        }

        $endDate = Carbon::parse($lastSaleDate);
        $startDate = $endDate->copy()->subDays(13); // Last 14 days

        // Fetch historical sales data for last 14 days
        $salesData = $this->getSalesData($employeeId, $startDate, $endDate);

        // Calculate historical metrics
        $daywiseSales = $this->getDaywiseSales($salesData, $startDate, $endDate);

        // Calculate current week sales
        $currentWeekStart = Carbon::today()->startOfWeek();
        $currentWeekSales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$currentWeekStart, Carbon::today()])
            ->sum('quantity');

        // Calculate best day and max sale
        $bestDay = null;
        $maxSale = 0;
        foreach ($daywiseSales as $day) {
            if ($day['total_qty'] > $maxSale) {
                $maxSale = $day['total_qty'];
                $bestDay = $day['date'];
            }
        }

        $topSellingProducts = $this->getTopSellingProducts($salesData);
        $frequentSellingPrices = $this->getFrequentSellingPrices($salesData);

        // Get predictions for next 10 weekdays (2 weeks)
        $weekdayPredictions = $this->getWeekdayPredictions($employeeId);

        // Get predictions for next 2 weekends
        $weekendPredictions = $this->getWeekendPredictions($employeeId);

        // Calculate daily target (25 cases per week = ~5 cases/day)
        $dailyTarget = 5;

        // Prepare chart data
        $chartData = $this->prepareChartData($daywiseSales);

        // Motivation message
        $motivation = $this->getMotivationMessage($employee, $weekdayPredictions, $weekendPredictions);

        return view('ai.predictive_sales_report', [
            'employees' => $employees,
            'employee' => $employee,
            'daywiseSales' => $daywiseSales,
            'currentWeekSales' => $currentWeekSales,
            'bestDay' => $bestDay,
            'maxSale' => $maxSale,
            'topSellingProducts' => $topSellingProducts,
            'frequentSellingPrices' => $frequentSellingPrices,
            'weekdayPredictions' => $weekdayPredictions,
            'weekendPredictions' => $weekendPredictions,
            'dailyTarget' => $dailyTarget,
            'chartData' => $chartData,
            'motivation' => $motivation,
        ]);
    }

    private function getSalesData($employeeId, Carbon $startDate, Carbon $endDate)
    {
        return DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    private function getDaywiseSales($sales, Carbon $startDate, Carbon $endDate)
    {
        $dailySales = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $totalQty = 0;

            foreach ($sales as $sale) {
                if (Carbon::parse($sale->date)->format('Y-m-d') === $dateStr) {
                    $totalQty += $sale->quantity;
                }
            }

            $dailySales[] = [
                'date' => $dateStr,
                'day_name' => $current->format('l'),
                'total_qty' => $totalQty,
                'is_weekend' => $current->isWeekend(),
            ];

            $current->addDay();
        }

        return $dailySales;
    }

    private function getTopSellingProducts($sales, $topN = 5)
    {
        $productSales = [];

        foreach ($sales as $sale) {
            $product = $sale->name;
            $productSales[$product] = ($productSales[$product] ?? 0) + $sale->quantity;
        }

        arsort($productSales);
        return array_slice($productSales, 0, $topN, true);
    }

    private function getFrequentSellingPrices($sales, $topN = 5)
    {
        $priceFrequency = [];

        foreach ($sales as $sale) {
            $price = $sale->price;
            $priceFrequency[(string)$price] = ($priceFrequency[(string)$price] ?? 0) + 1;
        }

        arsort($priceFrequency);
        return array_slice($priceFrequency, 0, $topN, true);
    }

    private function getWeekdayPredictions($employeeId)
    {
        $minWeekdays = 20; // Require at least 20 weekdays of data
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(90); // Look back 90 days

        // Fetch sales data for weekdays
        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereRaw('DAYOFWEEK(date) BETWEEN 2 AND 6') // Monday to Friday
            ->whereBetween('date', [$startDate, $today])
            ->orderBy('date', 'desc')
            ->get();

        // Group by date to count unique weekdays
        $groupedSales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        });

        if ($groupedSales->count() < $minWeekdays) {
            return [
                'error' => "Need at least {$minWeekdays} weekdays of sales data for accurate predictions",
                'days' => [],
                'total' => 0,
            ];
        }

        // Calculate daily averages and trend
        $averages = $this->calculateDailyAverages($sales);
        $trendFactor = $this->calculateTrendFactor($sales, $startDate, $today);

        // Generate predictions for next 10 weekdays
        return $this->generatePredictions($employeeId, 10, $averages, $trendFactor, false);
    }

    private function getWeekendPredictions($employeeId)
    {
        $minWeekendDays = 8; // Require at least 8 weekend days (4 weekends)
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(90); // Look back 90 days

        // Fetch sales data for weekends
        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereRaw('DAYOFWEEK(date) IN (1,7)') // Sunday (1) and Saturday (7)
            ->whereBetween('date', [$startDate, $today])
            ->orderBy('date', 'desc')
            ->get();

        // Group by date to count unique weekend days
        $groupedSales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        });

        if ($groupedSales->count() < $minWeekendDays) {
            return [
                'error' => "Need at least {$minWeekendDays} weekend days of sales data for accurate predictions",
                'days' => [],
                'total' => 0,
            ];
        }

        // Calculate daily averages and trend
        $averages = $this->calculateDailyAverages($sales);
        $trendFactor = $this->calculateTrendFactor($sales, $startDate, $today);

        // Generate predictions for next 4 weekend days (2 weekends)
        return $this->generatePredictions($employeeId, 4, $averages, $trendFactor, true);
    }

    private function calculateDailyAverages($sales)
    {
        $dayData = [
            'Monday' => ['sum' => 0, 'days' => 0],
            'Tuesday' => ['sum' => 0, 'days' => 0],
            'Wednesday' => ['sum' => 0, 'days' => 0],
            'Thursday' => ['sum' => 0, 'days' => 0],
            'Friday' => ['sum' => 0, 'days' => 0],
            'Saturday' => ['sum' => 0, 'days' => 0],
            'Sunday' => ['sum' => 0, 'days' => 0],
        ];

        // Group sales by date to sum quantities per day
        $dailyTotals = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        })->map(function ($daySales) {
            return $daySales->sum('quantity');
        });

        // Assign totals to days of the week
        foreach ($dailyTotals as $date => $total) {
            $dayOfWeek = Carbon::parse($date)->format('l');
            if (array_key_exists($dayOfWeek, $dayData)) {
                $dayData[$dayOfWeek]['sum'] += $total;
                $dayData[$dayOfWeek]['days']++;
            }
        }

        $averages = [];
        foreach ($dayData as $day => $data) {
            $averages[$day] = $data['days'] > 0 ? $data['sum'] / $data['days'] : 0;
        }

        return $averages;
    }

    private function calculateTrendFactor($sales, Carbon $startDate, Carbon $endDate)
    {
        // Split data into recent (last 30 days) and earlier periods
        $midPoint = $endDate->copy()->subDays(30);
        $recentSales = $sales->filter(function ($sale) use ($midPoint, $endDate) {
            return Carbon::parse($sale->date)->between($midPoint, $endDate);
        });
        $earlierSales = $sales->filter(function ($sale) use ($startDate, $midPoint) {
            return Carbon::parse($sale->date)->between($startDate, $midPoint);
        });

        // Calculate average daily sales for each period
        $recentDays = $recentSales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        })->count();
        $earlierDays = $earlierSales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        })->count();

        $recentAvg = $recentDays > 0 ? $recentSales->sum('quantity') / $recentDays : 0;
        $earlierAvg = $earlierDays > 0 ? $earlierSales->sum('quantity') / $earlierDays : 0;

        // Calculate trend factor (1.0 = no change, >1.0 = upward trend, <1.0 = downward trend)
        $trendFactor = $earlierAvg > 0 ? $recentAvg / $earlierAvg : 1.0;
        // Cap trend factor to prevent extreme adjustments
        return max(0.8, min(1.2, $trendFactor));
    }

    private function generatePredictions($employeeId, $numDays, $averages, $trendFactor, $isWeekend)
    {
        $predictions = [];
        $currentDate = Carbon::tomorrow();
        $total = 0;
        $count = 0;

        while ($count < $numDays) {
            $dayOfWeek = $currentDate->format('l');
            if (($isWeekend && $currentDate->isWeekend()) || (!$isWeekend && $currentDate->isWeekday())) {
                $basePrediction = $averages[$dayOfWeek] ?? 0;
                // Apply trend factor
                $predicted = $basePrediction * $trendFactor;
                // Round to nearest integer, ensure non-negative
                $predicted = max(0, round($predicted));

                // Fallback to overall average if prediction is 0
                if ($predicted == 0) {
                    $overallAvg = $this->calculateFallbackAverage($employeeId, $isWeekend);
                    $predicted = max(0, round($overallAvg * $trendFactor));
                }

                $predictions[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'day' => $dayOfWeek,
                    'predicted' => $predicted,
                ];

                $total += $predicted;
                $count++;
            }
            $currentDate->addDay();
        }

        return [
            'days' => $predictions,
            'total' => $total,
        ];
    }

    private function calculateFallbackAverage($employeeId, $isWeekend)
    {
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(90);

        $sales = DivanjSale::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $today])
            ->whereRaw($isWeekend ? 'DAYOFWEEK(date) IN (1,7)' : 'DAYOFWEEK(date) BETWEEN 2 AND 6')
            ->get();

        $dailyTotals = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->date)->format('Y-m-d');
        })->count();

        return $dailyTotals > 0 ? $sales->sum('quantity') / $dailyTotals : 0;
    }

    private function prepareChartData($daywiseSales)
    {
        $labels = [];
        $quantities = [];

        foreach ($daywiseSales as $day) {
            $labels[] = Carbon::parse($day['date'])->format('M d');
            $quantities[] = $day['total_qty'];
        }

        return [
            'labels' => $labels,
            'quantities' => $quantities,
        ];
    }

 

    private function getMotivationMessage($employee, $weekdayPredictions, $weekendPredictions)
    {
        $name = $employee->stage_name;
        $totalPredicted = ($weekdayPredictions['total'] ?? 0) + ($weekendPredictions['total'] ?? 0);
        $weeklyTarget = 25;
        $achievement = min(100, round(($totalPredicted / $weeklyTarget) * 100));

        $bestWeekday = '';
        $bestWeekdaySales = 0;
        foreach ($weekdayPredictions['days'] ?? [] as $day) {
            if ($day['predicted'] > $bestWeekdaySales) {
                $bestWeekdaySales = $day['predicted'];
                $bestWeekday = $day['day'];
            }
        }

        $messages = [
            // Script adherence focus
            "{$name}, your consistent approach is working! Stick to the script - greet warmly, confirm needs, and suggest complementary products. You're at {$achievement}% of target!",

            // Cross-selling strategy
            "{$name}, you're at {$achievement}% of target. Boost sales by upseeling & cross-selling - 'At this special price, can you squeeze 3 more cases?...' is an effective phrase. Your best day is {$bestWeekday} - capitalize on that energy!",

            // Personalization
            "{$name}, personalize every interaction. Use the customer's name 2-3 times naturally. 'But Mark, when your friend and family comes over..' This builds rapport.",

            // Pacing and tone
            "{$name}, maintain an enthusiastic tone throughout calls. Vary your pace - slower when explaining details, energetic when closing. You're on track for {$achievement}% of target!",

            // Continuous flow
            "Keep conversations flowing, {$name}. Avoid dead air - have transition phrases ready. 'I completely understand that you are not (mirror EXACT exceuse of customer)...' Your {$bestWeekday} performance shows this works!",

            // Product knowledge
            "{$name}, deep product knowledge drives sales. Highlight 3 key features for each item. You're at {$achievement}% - this extra detail could push you to 100%!",

            // Closing technique
            "Strong work {$name}! When closing, confirm his Address first: 'I have got you at the fake street, is that you?' rather than 'Would you like to buy?' Your {$bestWeekday} results prove this works!"
        ];

        return $messages[array_rand($messages)];
    }
}