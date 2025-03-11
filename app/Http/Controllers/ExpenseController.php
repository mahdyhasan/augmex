<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function listAllExpenses() {}
    public function recordNewExpense(Request $request) {}
    public function updateExpenseRecord(Request $request, $id) {}
    public function deleteExpense($id) {}
}
