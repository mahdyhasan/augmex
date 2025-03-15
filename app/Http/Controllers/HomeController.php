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

class HomeController extends Controller
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
    public function index()
    {
        return view('home');
    }
}
