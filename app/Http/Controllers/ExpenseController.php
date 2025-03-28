<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use DataTables;
use Excel;
use PDF; 

use App\Models\Account;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\ClientPayment;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

use App\Models\User;

class ExpenseController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('superadmin');
    }
    
    // List all expenses
    public function expensesIndex(Request $request)
    {
        $categories = ExpenseCategory::all();

        // Ensure only authorized users can access
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isHR() && !Auth::user()->isAccountant()) {
            return abort(403, 'Unauthorized Access');
        }

        // Get the date range from the request
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Fetch expenses filtered by date range
        $expenses = Expense::with('expenseCategory')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('expenses.index', compact('expenses', 'startDate', 'endDate', 'categories'));
    }

    // Store a new expense
    public function expensesStore(Request $request)
    {
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isHR() && !Auth::user()->isAccountant()) {
            return abort(403, 'Unauthorized Access');
        }

        // $request->validate([
        //     'expense_date' => 'required|date',
        //     'category_id' => 'required|exists:expense_categories,id',
        //     'description' => 'nullable|string|max:255',
        //     'amount' => 'required|numeric|min:0',
        //     'receipt' => 'nullable|image|max:800',
        // ]);

        // Handle file upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->move(public_path('assets/receipts'), $filename);
            if ($path) {
                $receiptPath = $filename;
            }
        }

        // Store the expense
        Expense::create([
            'expense_date' => $request->expense_date,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'receipt' => $receiptPath,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    // Edit expense record
    public function expensesEdit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    // Update expense record
    public function expensesUpdate(Request $request, $id)
    {
        // $request->validate([
        //     'expense_date' => 'required|date',
        //     'category_id' => 'required|exists:expense_categories,id',
        //     'description' => 'nullable|string|max:255',
        //     'amount' => 'required|numeric|min:0',
        //     'receipt' => 'nullable|image|max:800',
        // ]);

        $expense = Expense::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/receipts'), $filename);
            $expense->receipt = $filename;
        }

        $expense->update($request->except(['receipt']));

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    // Delete expense record
    public function expensesDestroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }



}