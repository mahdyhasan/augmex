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
use App\Models\Transaction;
use App\Models\User;

class AccountController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('superadmin');
    }


    // Bank Accounts Index
    public function bankAccountsIndex()
    {
        $bankAccounts = BankAccount::all();
        return view('bank_accounts.index', compact('bankAccounts'));
    }

    // Bank Accounts Edit
    public function bankAccountsEdit($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        return view('bank_accounts.edit', compact('bankAccount'));
    }

    // Bank Accounts Update
    public function bankAccountsUpdate(Request $request, $id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update($request->all());
        return redirect()->route('bank_accounts.index')->with('success', 'Bank Account updated successfully');
    }

    // Bank Accounts Store
    public function bankAccountsStore(Request $request)
    {
        BankAccount::create($request->all());
        return redirect()->route('bank_accounts.index')->with('success', 'Bank Account created successfully');
    }


    // Fixed Assets Index
    public function fixedAssetsIndex()
    {
        $fixedAssets = FixedAsset::all();
        return view('fixed_assets.index', compact('fixedAssets'));
    }

    public function fixedAssetsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'useful_life' => 'required|integer|min:1',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'depreciation_year' => 'required|integer',
            'depreciation_amount' => 'required|numeric|min:0',
        ]);

        // Create Fixed Asset
        $fixedAsset = FixedAsset::create($request->only(['name', 'purchase_date', 'cost', 'useful_life', 'depreciation_rate']));

        // Create Initial Depreciation Record
        DepreciationRecord::create([
            'asset_id' => $fixedAsset->id,
            'year' => $request->depreciation_year,
            'depreciation_amount' => $request->depreciation_amount,
        ]);

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed Asset and initial depreciation record added successfully.');
    }

    // Fixed Assets Edit Page
    public function fixedAssetsEdit($id)
    {
        $fixedAsset = FixedAsset::findOrFail($id);
        return view('fixed_assets.edit', compact('fixedAsset'));
    }

    // Fixed Assets Update
    public function fixedAssetsUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
        ]);

        $fixedAsset = FixedAsset::findOrFail($id);
        $fixedAsset->update($request->all());

        return redirect()->route('fixed_assets.index')->with('success', 'Fixed Asset updated successfully.');
    }



    // List all depreciation records for a fixed asset
    public function depreciationRecordsIndex($asset_id)
    {
        $fixedAsset = FixedAsset::findOrFail($asset_id);
        $depreciationRecords = DepreciationRecord::where('asset_id', $asset_id)->get();

        return view('depreciation_records.index', compact('fixedAsset', 'depreciationRecords'));
    }

    // Store new depreciation record
    public function depreciationRecordsStore(Request $request, $asset_id)
    {
        $request->validate([
            'year' => 'required|integer',
            'depreciation_amount' => 'required|numeric|min:0',
        ]);

        DepreciationRecord::create([
            'asset_id' => $asset_id,
            'year' => $request->year,
            'depreciation_amount' => $request->depreciation_amount,
        ]);

        return redirect()->route('depreciation_records.index', $asset_id)->with('success', 'Depreciation record added successfully.');
    }

    // Edit depreciation record
    public function depreciationRecordsEdit($id)
    {
        $depreciationRecord = DepreciationRecord::findOrFail($id);
        return view('depreciation_records.edit', compact('depreciationRecord'));
    }

    // Update depreciation record
    public function depreciationRecordsUpdate(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer',
            'depreciation_amount' => 'required|numeric|min:0',
        ]);

        $depreciationRecord = DepreciationRecord::findOrFail($id);
        $depreciationRecord->update($request->all());

        return redirect()->route('depreciation_records.index', $depreciationRecord->asset_id)
                        ->with('success', 'Depreciation record updated successfully.');
    }

    // Delete depreciation record
    public function depreciationRecordsDestroy($id)
    {
        $depreciationRecord = DepreciationRecord::findOrFail($id);
        $asset_id = $depreciationRecord->asset_id;
        $depreciationRecord->delete();

        return redirect()->route('depreciation_records.index', $asset_id)
                        ->with('success', 'Depreciation record deleted successfully.');
    }


        // List all petty cash records
        public function pettyCashIndex()
        {
            $pettyCashRecords = PettyCash::orderBy('date', 'desc')->get();
            return view('petty_cash.index', compact('pettyCashRecords'));
        }

        // Store a new petty cash record
        public function pettyCashStore(Request $request)
        {
            $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string|max:255',
            ]);

            PettyCash::create($request->all());

            return redirect()->route('petty_cash.index')->with('success', 'Petty Cash record added successfully.');
        }

        // Edit petty cash record
        public function pettyCashEdit($id)
        {
            $pettyCash = PettyCash::findOrFail($id);
            return view('petty_cash.edit', compact('pettyCash'));
        }

        // Update petty cash record
        public function pettyCashUpdate(Request $request, $id)
        {
            $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string|max:255',
            ]);

            $pettyCash = PettyCash::findOrFail($id);
            $pettyCash->update($request->all());

            return redirect()->route('petty_cash.index')->with('success', 'Petty Cash record updated successfully.');
        }

        // Delete petty cash record
        public function pettyCashDestroy($id)
        {
            PettyCash::findOrFail($id)->delete();
            return redirect()->route('petty_cash.index')->with('success', 'Petty Cash record deleted successfully.');
        }


        // List all liabilities
        public function liabilitiesIndex()
        {
            $liabilities = Liability::with('account')->orderBy('due_date', 'asc')->get();
            $accounts = Account::all(); // Fetch accounts for dropdown selection
            return view('liabilities.index', compact('liabilities', 'accounts'));
        }

        // Store a new liability record
        public function liabilitiesStore(Request $request)
        {
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'amount' => 'required|numeric|min:0',
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|string|max:50',
            ]);

            Liability::create($request->all());

            return redirect()->route('liabilities.index')->with('success', 'Liability added successfully.');
        }

        // Edit liability record
        public function liabilitiesEdit($id)
        {
            $liability = Liability::findOrFail($id);
            $accounts = Account::all();
            return view('liabilities.edit', compact('liability', 'accounts'));
        }

        // Update liability record
        public function liabilitiesUpdate(Request $request, $id)
        {
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'amount' => 'required|numeric|min:0',
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|string|max:50',
            ]);

            $liability = Liability::findOrFail($id);
            $liability->update($request->all());

            return redirect()->route('liabilities.index')->with('success', 'Liability updated successfully.');
        }

        // Delete liability record
        public function liabilitiesDestroy($id)
        {
            Liability::findOrFail($id)->delete();
            return redirect()->route('liabilities.index')->with('success', 'Liability deleted successfully.');
        }



        // List all transactions
        public function transactionsIndex()
        {
            $transactions = Transaction::with('account')->orderBy('transaction_date', 'desc')->get();
            $accounts = Account::all(); // Fetch accounts for dropdown selection
            return view('transactions.index', compact('transactions', 'accounts'));
        }

        // Store a new transaction record
        public function transactionsStore(Request $request)
        {
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'type' => 'required|in:Deposit,Withdrawal',
                'amount' => 'required|numeric|min:0',
                'reference' => 'nullable|string|max:255',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string|max:255',
            ]);

            Transaction::create($request->all());

            return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
        }

        // Edit transaction record
        public function transactionsEdit($id)
        {
            $transaction = Transaction::findOrFail($id);
            $accounts = Account::all();
            return view('transactions.edit', compact('transaction', 'accounts'));
        }

        // Update transaction record
        public function transactionsUpdate(Request $request, $id)
        {
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'type' => 'required|in:Deposit,Withdrawal',
                'amount' => 'required|numeric|min:0',
                'reference' => 'nullable|string|max:255',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string|max:255',
            ]);

            $transaction = Transaction::findOrFail($id);
            $transaction->update($request->all());

            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
        }

        // Delete transaction record
        public function transactionsDestroy($id)
        {
            Transaction::findOrFail($id)->delete();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        }



    // List all tax payments
    public function taxPaymentsIndex()
    {
        $taxPayments = TaxPayment::orderBy('payment_date', 'desc')->get();
        return view('tax_payments.index', compact('taxPayments'));
    }

    // Store a new tax payment
    public function taxPaymentsStore(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        TaxPayment::create($request->all());

        return redirect()->route('tax_payments.index')->with('success', 'Tax payment recorded successfully.');
    }

    // Edit tax payment record
    public function taxPaymentsEdit($id)
    {
        $taxPayment = TaxPayment::findOrFail($id);
        return view('tax_payments.edit', compact('taxPayment'));
    }

    // Update tax payment record
    public function taxPaymentsUpdate(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $taxPayment = TaxPayment::findOrFail($id);
        $taxPayment->update($request->all());

        return redirect()->route('tax_payments.index')->with('success', 'Tax payment updated successfully.');
    }

    // Delete tax payment record
    public function taxPaymentsDestroy($id)
    {
        TaxPayment::findOrFail($id)->delete();
        return redirect()->route('tax_payments.index')->with('success', 'Tax payment deleted successfully.');
    }




    public function incomeStatement(Request $request)
    {
        // Default to the current month if no date range is selected
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
    
        // Fetch revenue within the date range
        $revenues = Transaction::where('type', 'Deposit')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    
        // Fetch expenses within the date range
        $expenses = Expense::with('expenseCategory')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->groupBy('expenseCategory.name')
            ->map(function ($items) {
                return [
                    'category' => $items->first()->expenseCategory->name ?? 'Uncategorized',
                    'total_amount' => $items->sum('amount')
                ];
            });
    
        // Calculate total expenses
        $totalExpenses = $expenses->sum('total_amount');
    
        // Calculate net income
        $netIncome = $revenues - $totalExpenses;
    
        return view('accounts.incomeStatement', compact('startDate', 'endDate', 'revenues', 'expenses', 'totalExpenses', 'netIncome'));
    }
    








}

