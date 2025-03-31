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
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

use App\Models\User;

class AccountController extends Controller
{


    // Fixed exchange rates
    protected $exchangeRates = [
        'USD' => 120,
        'EUR' => 130,
        'GBP' => 145,
        'AUD' => 75,
        'BDT' => 1
    ];
    
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
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'purchase_date' => 'required|date',
        //     'cost' => 'required|numeric|min:0',
        //     'useful_life' => 'required|integer|min:1',
        //     'depreciation_rate' => 'required|numeric|min:0|max:100',
        //     'depreciation_year' => 'required|integer',
        //     'depreciation_amount' => 'required|numeric|min:0',
        // ]);

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
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'purchase_date' => 'required|date',
        //     'cost' => 'required|numeric|min:0',
        //     'depreciation_rate' => 'required|numeric|min:0|max:100',
        // ]);

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
        // $request->validate([
        //     'year' => 'required|integer',
        //     'depreciation_amount' => 'required|numeric|min:0',
        // ]);

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
        // $request->validate([
        //     'year' => 'required|integer',
        //     'depreciation_amount' => 'required|numeric|min:0',
        // ]);

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
            // $request->validate([
            //     'date' => 'required|date',
            //     'amount' => 'required|numeric|min:0',
            //     'description' => 'required|string|max:255',
            // ]);

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
            // $request->validate([
            //     'date' => 'required|date',
            //     'amount' => 'required|numeric|min:0',
            //     'description' => 'required|string|max:255',
            // ]);

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





    // List all tax payments
    public function taxPaymentsIndex()
    {
        $taxPayments = TaxPayment::orderBy('payment_date', 'desc')->get();
        return view('tax_payments.index', compact('taxPayments'));
    }

    // Store a new tax payment
    public function taxPaymentsStore(Request $request)
    {
        // $request->validate([
        //     'type' => 'required|string|max:50',
        //     'amount' => 'required|numeric|min:0',
        //     'payment_date' => 'required|date',
        // ]);

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
        // $request->validate([
        //     'type' => 'required|string|max:50',
        //     'amount' => 'required|numeric|min:0',
        //     'payment_date' => 'required|date',
        // ]);

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

        // Access control: allow only Admins and users whose employee->client_id equals 1
        if (!(Auth::user()->isSuperAdmin() )) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date'
        ]);

        // Default to current month if no dates provided
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

        // Get revenue data with currency conversion
        $revenuePayments = ClientPayment::with(['invoice.client.clientConditions'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        // Calculate revenue in original currency and BDT
        $revenueOriginal = 0;
        $revenueBdt = 0;

        foreach ($revenuePayments as $payment) {
            $currency = optional(optional($payment->invoice)->client)->clientConditions->first()->currency ?? 'USD';
            $revenueOriginal += $payment->amount;
            $revenueBdt += $payment->amount * ($this->exchangeRates[$currency] ?? 1);
        }

        // Get expenses by category (assuming expenses are in BDT)
        $expensesByCategory = ExpenseCategory::with(['expenses' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'amount' => $category->expenses->sum('amount')
                ];
            });

        $totalExpenses = $expensesByCategory->sum('amount');
        $netIncome = $revenueBdt - $totalExpenses;

        return view('accounts.incomeStatement', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'revenueOriginal' => $revenueOriginal,
            'revenueBdt' => $revenueBdt,
            'expensesByCategory' => $expensesByCategory,
            'totalExpenses' => $totalExpenses,
            'netIncome' => $netIncome,
            'exchangeRates' => $this->exchangeRates
        ]);
        }





}

