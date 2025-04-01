<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use DataTables;
use Excel;
use PDF; 

use App\Models\CareerApplicant;
use App\Exports\CareerApplicantsExport;


class CareerController extends Controller
{
    

    public function applyForPosition()
    {

        return view('career.apply');
    }

    public function storeCandidatesData(Request $request)
    {
        $validated = $request->validate([
            'position' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = CareerApplicant::where('position', $request->position)
                        ->where('email', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('You have already applied for this position with this email address.');
                    }
                }
            ],
            'phone' => [
                'required',
                'string',
                'size:11',
                'regex:/^0\d{10}$/',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = CareerApplicant::where('position', $request->position)
                        ->where('phone', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('You have already applied for this position with this phone number.');
                    }
                }
            ],
            'age' => 'required|integer|min:18|max:100',
            'last_education' => 'required|string|max:255',
            'last_education_year' => 'required|integer|digits:4',
            'last_education_institute' => 'required|string|max:255',
            'last_experience' => 'nullable|string|max:255',
            'total_experience' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'resume_upload' => 'required|file|mimes:pdf,doc,docx|max:4096',
        ]);
    
        if ($request->hasFile('resume_upload')) {
            $file = $request->file('resume_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/candidates', $filename);
            $validated['resume_upload'] = 'candidates/' . $filename;
        }
    
        CareerApplicant::create($validated);
    
        $success = "Your application has been submitted successfully.";
        return view('career.thank-you', compact('success'));
}
    


    public function careerPageIndex(Request $request)
    {
        $applicants = CareerApplicant::query()
            ->when($request->position, function($query, $position) {
                return $query->where('position', $position);
            })
            ->when($request->status !== null, function($query) use ($request) {
                return $query->where('shortlisted', $request->status);
            })
            ->when($request->experience, function($query, $experience) {
                return $query->where('total_experience', $experience);
            })
            ->when($request->education, function($query, $education) {
                return $query->where('last_education', $education);
            })
            ->when($request->start_date, function($query, $startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($request->end_date, function($query, $endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->get();
    
        // Get unique positions for the filter dropdown
        $positions = CareerApplicant::distinct()->pluck('position');
    
        return view('career.index', compact('applicants', 'positions'));
    }

    

    public function updateStatus(Request $request, $id)
    {
        $applicant = CareerApplicant::findOrFail($id);
        $applicant->shortlisted = $request->status;
        $applicant->save();
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return back()->with('success', 'Applicant status updated successfully');
    }

    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:500'
        ]);

        $applicant = CareerApplicant::findOrFail($id);
        $applicant->notes = $request->note;
        $applicant->save();

        return back()->with('success', 'Note added successfully');
    }


    // public function export(Request $request)
    // {
    //     $status = $request->input('status');
        
    //     $filename = 'candidates_'.now()->format('Ymd_His').'.xlsx';
        
    //     return Excel::download(new CareerApplicantsExport($status), $filename);
    // }


    public function export(Request $request)
    {
        $status = $request->input('status');
        $export = new CareerApplicantsExport($status);
        
        if (class_exists(\Maatwebsite\Excel\Excel::class)) {
            $filename = 'candidates_'.now()->format('Ymd_His').'.xlsx';
            return \Excel::download($export, $filename);
        }
        
        // Fallback to CSV
        $filename = 'candidates_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function() use ($export) {
            $export->collection()->each(function($item) {
                echo implode(',', $item) . "\n";
            });
        }, $filename);
    }












}
