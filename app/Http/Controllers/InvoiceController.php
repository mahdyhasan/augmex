<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Invoice;

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