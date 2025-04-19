<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeIncentiveFine;
use App\Models\IncentiveFineType;
use Illuminate\Http\Request;

class EmployeeIncentiveFineController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeIncentiveFine::with(['employee', 'type'])
            ->orderBy('date', 'desc');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('type')) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        $transactions = $query->paginate(20);

        $employees = Employee::all();
        $types = IncentiveFineType::all();

        return view('incentives_fines.index', compact('transactions', 'employees', 'types'));
    }

    // Optional: You can remove this if the modal is used instead of a separate create page
    public function create()
    {
        $employees = Employee::all();
        $types = IncentiveFineType::all();

        return view('incentives_fines.create', compact('employees', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'transaction_type_id' => 'required|exists:incentive_fine_types,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        // Create the transaction with the correct column mapping
        EmployeeIncentiveFine::create([
            'employee_id' => $validated['employee_id'],
            'incentive_fine_type_id' => $validated['transaction_type_id'],
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('employees.incentives.fines')->with('success', 'Transaction added successfully.');
    }

    public function summary(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $query = EmployeeIncentiveFine::with('type')
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $transactions = $query->get();

        $totalIncentives = $transactions->where('type.type', 'incentive')->sum('amount');
        $totalFines = $transactions->where('type.type', 'fine')->sum('amount');
        $net = $totalIncentives - $totalFines;

        return response()->json([
            'incentives' => number_format($totalIncentives, 2),
            'fines' => number_format($totalFines, 2),
            'net' => number_format($net, 2),
        ]);
    }



}
