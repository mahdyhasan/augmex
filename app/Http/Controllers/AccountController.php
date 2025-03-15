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


class AccountController extends Controller
{
    public function listAllAccounts() {}
    public function viewAccountDetails($id) {}
    public function createNewAccount(Request $request) {}
    public function updateAccountDetails(Request $request, $id) {}
    public function deleteAccount($id) {}



    public function incomeStatement() {
        return view('accounts.incomeStatement');
    }
    
    
}
