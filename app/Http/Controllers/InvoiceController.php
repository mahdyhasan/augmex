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
use App\Models\InvoiceItem;
use App\Models\Liability;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;
use App\Models\Transaction;
use App\Models\User;


class InvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('superadmin');
    }


        // List all invoices
        public function invoicesIndex()
        {
            $invoices = Invoice::with('client')->orderBy('invoice_date', 'desc')->get();
            return view('invoices.index', compact('invoices'));
        }
    
        // Show invoice generation page
        public function invoiceGeneration()
        {
            $clients = Client::all();
            return view('invoices.invoice_generation', compact('clients'));
        }
    
        // Generate invoice based on user input
        public function generateInvoice(Request $request)
        {
            // $request->validate([
            //     'client_id' => 'required|exists:clients,id',
            //     'start_date' => 'required|date',
            //     'end_date' => 'required|date|after_or_equal:start_date',
            // ]);
        
            $client = Client::findOrFail($request->client_id);
            $clientCondition = ClientCondition::where('client_id', $client->id)->first();
            $employees = Employee::where('client_id', $client->id)->get();
        
            $totalHours = 0;
            $totalAmount = 0;
            $invoiceItems = [];
        
            foreach ($employees as $employee) {
                $hoursWorked = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get()
                    ->sum(function ($attendance) {
                        return (strtotime($attendance->check_out) - strtotime($attendance->check_in)) / 3600;
                    });
            
                if ($hoursWorked > 0) {
                    $rate = $clientCondition->rate ?? 0;
                    $amount = $hoursWorked * $rate;
                    $totalHours += $hoursWorked;
                    $totalAmount += $amount;
            
                    // Fetch employee name from users table
                    $employeeName = $employee->user->name ?? 'Unknown Employee';
            
                    $invoiceItems[] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employeeName, // ✅ Now fetched from `users` table
                        'days_worked' => floor($hoursWorked / 8),
                        'hours_worked' => $hoursWorked,
                        'rate' => $rate,
                        'deductions' => 0,
                        'commission' => 0,
                        'amount' => $amount,
                    ];
                }
            }
            
            
            // Save invoice first before inserting items
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'invoice_date' => now(),
                'work_start_date' => $request->start_date,
                'work_end_date' => $request->end_date,
                'total_amount' => $totalAmount,
                'amount_in_words' => ucwords($this->convertNumberToWords($totalAmount)) . ' only',
            ]);

            // Now `$invoice` exists, so we can insert invoice items
            foreach ($invoiceItems as $item) {
                $invoice->invoiceItems()->create($item);
            }      
        
            return redirect()->route('invoices.view', $invoice->id);
        }
        
        
        // Convert numbers to words (for invoice total)
        private function convertNumberToWords($number)
        {
            $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            return $f->format($number);
        }

        

    
        // Edit invoice record
        public function invoicesEdit($id)
        {
            $invoice = Invoice::findOrFail($id);
            $clients = Client::all();
            return view('invoices.edit', compact('invoice', 'clients'));
        }
    


        public function invoicesUpdate(Request $request, $id)
        {
            // $request->validate([
            //     'client_id' => 'required|exists:clients,id',
            //     'invoice_date' => 'required|date',
            //     'total_amount' => 'required|numeric|min:0',
            //     'items.*.days_worked' => 'required|integer|min:0',
            //     'items.*.hours_worked' => 'required|numeric|min:0',
            //     'items.*.rate' => 'required|numeric|min:0',
            //     'items.*.deductions' => 'nullable|numeric|min:0',
            //     'items.*.commission' => 'nullable|numeric|min:0',
            //     'items.*.amount' => 'required|numeric|min:0',
            // ]);

            $invoice = Invoice::findOrFail($id);
            $invoice->update([
                'client_id' => $request->client_id,
                'invoice_date' => $request->invoice_date,
                'total_amount' => $request->total_amount,
            ]);

            // Update each invoice item
            foreach ($request->items as $index => $itemData) {
                $invoiceItem = InvoiceItem::findOrFail($itemData['id']);
                $invoiceItem->update([
                    'days_worked' => $itemData['days_worked'],
                    'hours_worked' => $itemData['hours_worked'],
                    'rate' => $itemData['rate'],
                    'deductions' => $itemData['deductions'] ?? 0,
                    'commission' => $itemData['commission'] ?? 0,
                    'amount' => $itemData['amount'],
                ]);
            }

            return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
        }


    
        // View the final invoice
        public function viewInvoice($id)
        {
            $invoice = Invoice::with('client', 'invoiceItems')->findOrFail($id);
            return view('invoices.invoice', compact('invoice'));
        }











}