<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\User;

class AccountController extends Controller
{
    public function listAllAccounts() {}
    public function viewAccountDetails($id) {}
    public function createNewAccount(Request $request) {}
    public function updateAccountDetails(Request $request, $id) {}
    public function deleteAccount($id) {}

    
}
