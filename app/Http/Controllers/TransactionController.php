<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function listAllTransactions() {}
    public function recordNewTransaction(Request $request) {}
    public function updateTransactionDetails(Request $request, $id) {}
    public function deleteTransaction($id) {}
}
