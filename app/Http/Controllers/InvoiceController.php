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


class InvoiceController extends Controller
{
    public function listInvoices()
    {
        return response()->json(Invoice::all());
    }

    public function createInvoice(Request $request)
    {
        $invoice = Invoice::create($request->all());
        return response()->json($invoice, 201);
    }

    public function viewInvoice($id)
    {
        return response()->json(Invoice::findOrFail($id));
    }

    public function updateInvoice(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());
        return response()->json($invoice);
    }

    public function deleteInvoice($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}