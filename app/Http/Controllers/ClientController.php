<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;

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


class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('superadmin');
    }
    
    public function index()
    {
        // Get all clients with their client conditions
        $clients = Client::with('clientConditions')->get();

        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        // Validation and storing a new client
        // $request->validate([
        //     'company_name' => 'required',
        //     'country' => 'required',
        //     'kdm' => 'required',
        //     'status' => 'required',
        //     'rate' => 'required',
        //     'rate_type' => 'required',
        //     'invoice_type' => 'required',
        // ]);

        $client = new Client();
        $client->agency = $request->agency;
        $client->company = $request->company_name;
        $client->country = $request->country;
        $client->kdm = $request->kdm;
        $client->status = 1;
        $client->save();

        // Add associated ClientCondition
        $condition = new ClientCondition();
        $condition->client_id = $client->id;
        $condition->rate = $request->rate;
        $condition->currency = $request->currency;
        $condition->rate_type = $request->rate_type;
        $condition->invoice_type = $request->invoice_type;
        $condition->save();

        return redirect()->route('clients.index')->with('success', 'Client added successfully');
    }

    public function edit($id)
    {
        // Fetch the client by ID
        $client = Client::with('clientConditions')->findOrFail($id);
    
        // Pass the client data to the edit view
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        // $request->validate([
        //     'company_name' => 'required',
        //     'country' => 'required',
        //     'kdm' => 'required',
        //     'rate' => 'required',
        //     'rate_type' => 'required',
        //     'invoice_type' => 'required',
        //     'currency' => 'required',
        // ]);
    
        // Find the client
        $client = Client::findOrFail($id);
        $client->agency = $request->agency;
        $client->company = $request->company_name;
        $client->country = $request->country;
        $client->kdm = $request->kdm;
        $client->status = $request->status;
        $client->save();
    
        // Find the client's condition and update it
        $condition = $client->clientConditions()->first();
        if ($condition) {
            $condition->rate = $request->rate;
            $condition->rate_type = $request->rate_type;
            $condition->invoice_type = $request->invoice_type;
            $condition->currency = $request->currency;
            $condition->save();
        }
    
        // Redirect back to the clients list with a success message
        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }    
    
    public function destroy($id)
    {
        // Find the client by ID
        $client = Client::findOrFail($id);
    
        // Update the client status to 0 (inactive)
        $client->status = 0;
        $client->save();
    
        // Redirect back with success message
        return redirect()->route('clients.index')->with('success', 'Client status updated to inactive');
    }
    
    



    
    // List all client payments
    public function clientPaymentsIndex()
    {
        $clientPayments = ClientPayment::with('invoice')->orderBy('payment_date', 'desc')->get();
        $invoices = Invoice::with('client')->get();
        return view('client_payments.index', compact('clientPayments', 'invoices'));
    }


    // Store a new client payment
    public function clientPaymentsStore(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Create payment
            ClientPayment::create($request->all());
            
            // Update invoice status using the invoice_id from the form
            Invoice::where('id', $request->invoice_id)->update([
                'status' => 'paid',
            ]);
        });
        
        return redirect()->route('client_payments.index')
               ->with('success', 'Client payment recorded successfully.');
    }



    // Edit client payment record
    public function clientPaymentsEdit($id)
    {
        $clientPayment = ClientPayment::findOrFail($id);
        $invoices = Invoice::all();
        return view('client_payments.edit', compact('clientPayment', 'invoices'));
    }

    // Update client payment record
    public function clientPaymentsUpdate(Request $request, $id)
    {
        // $request->validate([
        //     'invoice_id' => 'required|exists:invoices,id',
        //     'amount' => 'required|numeric|min:0',
        //     'payment_date' => 'required|date',
        //     'method' => 'required|string|max:50',
        // ]);

        $clientPayment = ClientPayment::findOrFail($id);
        $clientPayment->update($request->all());

        return redirect()->route('client_payments.index')->with('success', 'Client payment updated successfully.');
    }

    // Delete client payment record
    public function clientPaymentsDestroy($id)
    {
        ClientPayment::findOrFail($id)->delete();
        return redirect()->route('client_payments.index')->with('success', 'Client payment deleted successfully.');
    }









}


