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
use App\Models\DivanjCommission;
use App\Models\DivanjSale;
use App\Models\DepreciationRecord;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payroll;
use App\Models\PettyCash;
use App\Models\TaxPayment;

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
        // public function generateInvoice(Request $request)
        // {
        //     $client = Client::findOrFail($request->client_id);
        //     $clientCondition = ClientCondition::where('client_id', $client->id)->first();
        //     $employees = Employee::where('client_id', $client->id)->get();

        //     $totalHours = 0;
        //     $totalAmount = 0;
        //     $invoiceItems = [];

        //     foreach ($employees as $employee) {
        //         $attendances = Attendance::where('employee_id', $employee->id)
        //             ->whereBetween('date', [$request->start_date, $request->end_date])
        //             ->get();

        //         $employeeHours = 0;
        //         $workedDays = 0;

        //         foreach ($attendances as $attendance) {
        //             if ($attendance->check_in && $attendance->check_out) {
        //                 $seconds = strtotime($attendance->check_out) - strtotime($attendance->check_in);
        //                 $hours = $seconds / 3600;

        //                 // Cap at 8 hours
        //                 $finalHours = ($hours >= 8) ? 8 : floor($hours * 2) / 2;

        //                 $employeeHours += $finalHours;

        //                 if ($finalHours > 0) {
        //                     $workedDays++;
        //                 }
        //             }
        //         }

        //         if ($employeeHours > 0) {
        //             $rate = $clientCondition->rate ?? 0;
        //             $amount = $employeeHours * $rate;
        //             $totalHours += $employeeHours;
        //             $totalAmount += $amount;

        //             $invoiceItems[] = [
        //                 'employee_id'   => $employee->id,
        //                 'employee_name' => $employee->stage_name ?? 'Unknown',
        //                 'days_worked'   => $workedDays,
        //                 'hours_worked'  => $employeeHours,
        //                 'rate'          => $rate,
        //                 'deductions'    => 0,
        //                 'commission'    => 0,
        //                 'amount'        => $amount,
        //             ];
        //         }
        //     }

        //     // Improved invoice number generation with safety limit
        //     $invoiceNumber = $this->generateUniqueInvoiceNumber();

            
        //     // Save invoice first before inserting items
        //     $invoice = Invoice::create([
        //         'client_id' => $client->id,
        //         'invoice_no' => $invoiceNumber,
        //         'invoice_date' => now(),
        //         'work_start_date' => $request->start_date,
        //         'work_end_date' => $request->end_date,
        //         'total_amount' => $totalAmount,
        //     ]);

        //     // Insert invoice items
        //     foreach ($invoiceItems as $item) {
        //         $invoice->invoiceItems()->create($item);
        //     }


        //     return redirect()->route('invoices.view', $invoice->id);
        // }

        
        public function generateInvoice(Request $request)
        {
            $client = Client::findOrFail($request->client_id);
            $clientCondition = ClientCondition::where('client_id', $client->id)->first();
            $employees = Employee::where('client_id', $client->id)->get();
        
            $totalHours = 0;
            $totalAmount = 0;
            $invoiceItems = [];
        
            foreach ($employees as $employee) {
                // Get employee's commission for this period
                $commission = DivanjCommission::where('employee_id', $employee->id)
                    ->where(function($query) use ($request) {
                        $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                              ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('start_date', '<=', $request->start_date)
                                    ->where('end_date', '>=', $request->end_date);
                              });
                    })
                    ->sum('commission_amount') ?? 0;
        
                // Calculate attendance and hours
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get();
        
                $employeeHours = 0;
                $workedDays = 0;
        
                foreach ($attendances as $attendance) {
                    if ($attendance->check_in && $attendance->check_out) {
                        $seconds = strtotime($attendance->check_out) - strtotime($attendance->check_in);
                        $hours = $seconds / 3600;
                        $finalHours = ($hours >= 8) ? 8 : floor($hours * 2) / 2;
                        $employeeHours += $finalHours;
                        if ($finalHours > 0) $workedDays++;
                    }
                }
        
                if ($employeeHours > 0) {
                    $rate = $clientCondition->rate ?? 0;
                    $baseAmount = $employeeHours * $rate;
                    $deductions = 0; // Initialize deductions (you can add logic here)
                    
                    // THE CRUCIAL CHANGE - Correct calculation
                    $amount = $baseAmount + $commission - $deductions;
                    
                    $totalHours += $employeeHours;
                    $totalAmount += $amount;
        
                    $invoiceItems[] = [
                        'employee_id'    => $employee->id,
                        'employee_name'  => $employee->stage_name ?? 'Unknown',
                        'days_worked'    => $workedDays,
                        'hours_worked'   => $employeeHours,
                        'rate'           => $rate,
                        'deductions'     => $deductions,
                        'commission'     => $commission,
                        'amount'         => $amount, // Now correctly calculated
                    ];
                }
            }
        
            // Create invoice (keeping your existing table structure)
            $invoice = Invoice::create([
                'client_id'       => $client->id,
                'invoice_no'      => $this->generateUniqueInvoiceNumber(),
                'invoice_date'    => now(),
                'work_start_date' => $request->start_date,
                'work_end_date'   => $request->end_date,
                'total_amount'    => $totalAmount, // Final calculated amount
            ]);
        
            // Insert invoice items
            foreach ($invoiceItems as $item) {
                $invoice->invoiceItems()->create($item);
            }
        
            return redirect()->route('invoices.view', $invoice->id);
        }        
        



        protected function generateUniqueInvoiceNumber($maxAttempts = 100)
        {
            $prefix = 'INV-' . date('Ymd') . '-';
            $maxAttempts = 10;
            $attempts = 0;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $randomNum = mt_rand(1, 9999);
                $lastDigit = $randomNum % 10;
                
                // Adjust last digit to be 5-9 if it's 0-4
                if ($lastDigit <= 4) {
                    $randomNum += (5 - $lastDigit);
                }
                
                $suffix = str_pad($randomNum, 4, '0', STR_PAD_LEFT);
                $invoiceNumber = $prefix . $suffix;
                
                if (!Invoice::where('invoice_no', $invoiceNumber)->exists()) {
                    return $invoiceNumber;
                }
            }
            
            throw new \RuntimeException("Failed to generate unique invoice number after {$maxAttempts} attempts");
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
    // Optional but recommended validation
    // $request->validate([
    //     'client_id' => 'required|exists:clients,id',
    //     'invoice_date' => 'required|date',
    //     'total_amount' => 'required|numeric|min:0',
    //     'items.*.id' => 'required|exists:invoice_items,id',
    //     'items.*.days_worked' => 'required|integer|min:0',
    //     'items.*.hours_worked' => 'required|numeric|min:0',
    //     'items.*.rate' => 'required|numeric|min:0',
    //     'items.*.deductions' => 'nullable|numeric|min:0',
    //     'items.*.commission' => 'nullable|numeric|min:0',
    //     'items.*.amount' => 'required|numeric|min:0',
    // ]);

    // Find and update invoice
    $invoice = Invoice::findOrFail($id);
    $invoice->update([
        'client_id' => $request->client_id,
        'invoice_date' => $request->invoice_date,
        'total_amount' => $request->total_amount, // even though it's varchar, keeping it numeric
    ]);

    // Update each invoice item
    foreach ($request->items as $itemData) {
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
             $invoice = Invoice::with(['client', 'invoiceItems'])->findOrFail($id);

            $clientCondition = ClientCondition::where('client_id', $invoice->client_id)->first();
            $currency = $clientCondition->currency ?? '$';
        
            return view('invoices.invoice', compact('invoice', 'currency'));
    
        }











}