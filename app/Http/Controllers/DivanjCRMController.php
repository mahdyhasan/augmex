<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use DataTables;
use Excel;
use PDF; 

// Models
use App\Models\DivanjCrmLead;
use App\Models\DivanjCrmCallStatus;
use App\Models\DivanjCrmCallHistory;
use App\Models\DivanjCrmOrderHistory;
use App\Models\DivanjCrmFollowup;
use App\Models\DivanjCrmCallBackSheet;
use App\Models\DivanjCrmPaymentDetail;
use App\Models\User;

use App\Imports\DivanjLeadImport;

class DivanjCRMController extends Controller
{


    public function leadIndex(Request $request)
    {

        if ($request->ajax()) {
            $query = DivanjCrmLead::with('agent')
                ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                    $q->where('agent_id', auth()->id());
                });

            return DataTables::of($query)
                ->addColumn('action', function ($lead) {
                    return '<a href="' . route('divanj.crm.leads.show', $lead->id) . '" class="btn btn-sm btn-primary" target="_blank">View</a>';
                })
                ->make(true);
        }

        return view('divanj.crm.leads.index');
    }



    public function leadShow(DivanjCrmLead $lead)
    {
        if (!auth()->user()->isSuperAdmin() && $lead->agent_id !== auth()->id()) {
            abort(403);
        }

        $openCartToken = User::select('open_cart_token')->where('users.id', auth()->id())->get();

        $callHistories = $lead->callHistories()->with('callStatus')->get();
        $followups = $lead->followups()->get();
        $orders = $lead->orderHistories()->get();
        $payment = $lead->paymentDetails()->first();
        $callStatuses = DivanjCrmCallStatus::all();

        return view('divanj.crm.leads.show', compact('lead', 'callHistories', 'followups', 'orders', 'payment', 'callStatuses', 'openCartToken'));
    }

    public function leadEdit($id)
    {
        $lead = DivanjCrmLead::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && $lead->agent_id !== auth()->id()) {
            abort(403);
        }

        return view('divanj.crm.leads.edit', compact('lead'));
    }

    public function leadUpdate(Request $request, $id)
    {
        $lead = DivanjCrmLead::findOrFail($id);

        if (!auth()->user()->isSuperAdmin() && $lead->agent_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:11',
            'landline' => 'required|string|max:11',
            'source' => 'required|in:sam,pm,other',
        ]);

        $lead->update($validated);

        return redirect()->route('divanj.crm.leads.show', $lead->id)->with('success', 'Lead updated successfully');
    }


    public function addLeadForm()
    {
        $agents = [];
        
        if (auth()->user()->isSuperAdmin()) {
            $agents = User::with('employee')
                ->get();
        }

        return view('divanj.crm.leads.addNewLead', compact('agents'));
    }

    
    public function leadStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[1-9]\d{8,10}$/', $value)) {
                        $fail('The mobile number must be 9-11 digits and not start with 0');
                    }
    
                    if ($value && DivanjCrmLead::where('mobile', $value)->orWhere('landline', $value)->exists()) {
                        $fail('This number already exists in the system.');
                    }
                }
            ],
            'landline' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/^[1-9]\d{8,10}$/', $value)) {
                        $fail('The landline number must be 9-11 digits and not start with 0');
                    }
    
                    if ($value && DivanjCrmLead::where('mobile', $value)->orWhere('landline', $value)->exists()) {
                        $fail('This number already exists in the system.');
                    }
                }
            ],
            'source' => 'required|in:sam,pm,other',
            'note' => 'nullable|string',
            'excel_file' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',
            'agent_id' => 'nullable|exists:users,id'
        ]);
    
        // Handle Excel Upload
        if ($request->hasFile('excel_file')) {
            return $this->processExcelUpload($request->file('excel_file'), $validated['agent_id'] ?? null);
        }
    
        // Create Lead
        $lead = DivanjCrmLead::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'mobile'     => $validated['mobile'],
            'landline'   => $validated['landline'],
            'source'     => $validated['source'],
            'note'       => $validated['note'],
            'agent_id'   => $validated['agent_id'] ?? auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead
        ]);
    }
    
    
    private function processExcelUpload($file, $agentId = null)
    {
        try {
            Excel::import(new DivanjLeadImport($agentId), $file);
            
            return response()->json([
                'success' => true,
                'message' => 'Leads imported successfully',
                'redirect' => route('divanj.crm.leads.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing leads: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkPhoneNumber(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^[1-9][0-9]{8,10}$/']
        ]);
    
        $phone = $request->input('phone');
    
        $lead = DivanjCrmLead::where('mobile', $phone)
            ->orWhere('landline', $phone)
            ->with('agent')
            ->first();
    
        if ($lead) {
            $agentName = $lead->agent->name ?? 'Unknown agent';
    
            return response()->json([
                'exists' => true,
                'name' => $agentName,
            ]);
        }
    
        return response()->json([
            'exists' => false
        ]);
    }

    public function pmLeads(Request $request)
    {
        if ($request->ajax()) {
            $query = DivanjCrmLead::with('agent')
                ->where('source', 'pm')
                ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                    $q->where('agent_id', auth()->id());
                });

            return DataTables::of($query)
                ->addColumn('action', function ($lead) {
                    return '<a href="' . route('divanj.crm.leads.show', $lead->id) . '" class="btn btn-sm btn-primary">View</a>';
                })
                ->make(true);
        }

        return view('divanj.crm.leads.pm');
    }



    public function followupIndex(Request $request)
    {
        $callStatuses = DivanjCrmCallStatus::all();

        if ($request->ajax()) {
            $query = DivanjCrmFollowup::with(['lead', 'agent'])
                ->where('status', 0)
                ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                    $q->where('divanj_crm_followup.agent_id', auth()->id());
                });
    
            // Handle date range filtering
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $startDate = $request->input('start_date') ?: Carbon::today()->toDateString();
                $endDate = $request->input('end_date') ?: Carbon::today()->toDateString();
                $query->whereBetween('schedule_date', [$startDate, $endDate]);
            }



            return DataTables::of($query)
                ->addColumn('action', function ($followup) {
                    return '
                        <a href="' . route('divanj.crm.leads.show', $followup->lead_id) . '" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-success call-report-btn" data-followup-id="' . $followup->id . '" data-lead-id="' . $followup->lead_id . '" data-bs-toggle="modal" data-bs-target="#addCallReportModal">
                            <i class="fas fa-phone-alt"></i>
                        </button>';
                })
                ->make(true);
        }

        return view('divanj.crm.followup', compact('callStatuses'));
    }



    public function callBackSheet(Request $request)
    {
        if ($request->ajax()) {
            $query = DivanjCrmCallBackSheet::with(['lead', 'agent'])
                ->when(!auth()->user()->isSuperAdmin(), function ($q) {
                    $q->where('agent_id', auth()->id());
                });

            return DataTables::of($query)
                ->addColumn('action', function ($callBackSheet) {
                    return '<a href="' . route('divanj.crm.leads.show', $callBackSheet->lead_id) . '" class="btn btn-sm btn-primary">Call Report</a>';
                })
                ->make(true);
        }

        return view('divanj.crm.call_back_sheet');
    }



    public function paymentMethodUpdate(Request $request, $lead_id)
    {
        $validated = $request->validate([
            'cardboard' => 'required|string|max:20|regex:/^[0-9]+$/',
            'expiry_month' => 'required|numeric|between:1,12',
            'expiry_year' => 'required|numeric|between:25,40',
            'sivivi' => 'nullable|string|max:4|regex:/^[0-9]+$/',
            'card_type' => 'nullable|string',
        ]);
    
        // Format month to 2 digits
        $validated['expiry_month'] = str_pad($validated['expiry_month'], 2, '0', STR_PAD_LEFT);
    
        try {
            $payment = DivanjCrmPaymentDetail::updateOrCreate(
                ['lead_id' => $lead_id],
                $validated
            );
    
            return redirect()->back()->with('success', 'Payment details updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving payment details: '.$e->getMessage());
        }
    }



    public function storeCallReport(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:divanj_crm_leads,id',
            'medium' => 'required|in:call,text',
            'call_status_id' => 'required|exists:divanj_crm_call_statuses,id',
            'comment' => 'nullable|string',
            'schedule_date' => 'nullable|date',
            'schedule_time' => 'nullable|string',
        ]);
    
        // Always create call history record
        DivanjCrmCallHistory::create([
            'lead_id' => $validated['lead_id'],
            'agent_id' => auth()->id(),
            'medium' => $validated['medium'],
            'call_status_id' => $validated['call_status_id'],
            'comment' => $validated['comment'],
        ]);
    
        // Handle followup only if schedule date or time is provided
        if ($request->filled('schedule_date') || $request->filled('schedule_time')) {
            // Update existing followups for the same lead with status 0 or 2 to status 1
            DivanjCrmFollowup::where('lead_id', $validated['lead_id'])
                ->whereIn('status', [0, 2])
                ->update(['status' => 1]);
    
            // Create new followup record
            DivanjCrmFollowup::create([
                'lead_id' => $validated['lead_id'],
                'agent_id' => auth()->id(),
                'schedule_date' => $validated['schedule_date'],
                'schedule_time' => $validated['schedule_time'],
                'status' => 0,
                'notes' => $validated['comment'],
            ]);
        }
    
        return redirect()->back()->with('success', 'Call report added successfully');
    }


    
}