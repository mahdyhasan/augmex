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


class ClientController extends Controller
{
    public function listClients()
    {
        return response()->json(Client::all());
    }

    public function createClient(Request $request)
    {
        $client = Client::create($request->all());
        return response()->json($client, 201);
    }

    public function viewClient($id)
    {
        return response()->json(Client::findOrFail($id));
    }

    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($request->all());
        return response()->json($client);
    }

    public function deleteClient($id)
    {
        Client::findOrFail($id)->delete();
        return response()->json(['message' => 'Client deleted successfully']);
    }


    
}


