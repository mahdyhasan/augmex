<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\ClientCondition;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\EmployeeDetails;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;
use App\Models\Transaction;
use App\Models\User;


class ExpenseController extends Controller
{

    public function listAllExpenses(Request $request)
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

    public function recordNewExpense (Request $request)
    {
        // Ensure only authorized users can store expenses
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isHR() && !Auth::user()->isAccountant()) {
            return abort(403, 'Unauthorized Access');
        }

        // Validate the input
        $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'receipt' => 'nullable|image|max:800', 
        ]);

        // Handle file upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = time() . '_' . $file->getClientOriginalName();
        
            // Store the file in the public/assets/receipts directory
            $path = $file->move(public_path('assets/receipts'), $filename);
        
            // Check if the file was uploaded successfully
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

        return redirect()->back()->with('success', 'Expense recorded successfully.');
    }


}
