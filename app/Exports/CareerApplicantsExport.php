<?php

namespace App\Exports;

use App\Models\CareerApplicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CareerApplicantsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;
    
    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = CareerApplicant::query()
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'position',
                'total_experience',
                'last_education',
                'last_education_year',
                'shortlisted',
                'created_at',
                'resume_upload'
            ]);
        
        if ($this->status) {
            switch ($this->status) {
                case 'new':
                    $query->where('shortlisted', 0);
                    break;
                case 'shortlisted':
                    $query->where('shortlisted', 1);
                    break;
                case 'interview':
                    $query->where('shortlisted', 3);
                    break;
                case 'mock':
                    $query->where('shortlisted', 4);
                    break;
            }
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Position',
            'Experience',
            'Education',
            'Education Year',
            'Status',
            'Applied Date',
            'Resume URL'
        ];
    }

    public function map($applicant): array
    {
        $statusMap = [
            0 => 'New',
            1 => 'Shortlisted',
            2 => 'Rejected',
            3 => 'Interview',
            4 => 'Mock Calls',
            5 => 'Hired'
        ];
        
        $resumeUrl = $applicant->resume_upload 
            ? asset('storage/app/public/' . $applicant->resume_upload)
            : 'No resume';
        
        return [
            $applicant->id,
            $applicant->name,
            $applicant->email,
            $applicant->phone,
            $applicant->position,
            $applicant->total_experience,
            $applicant->last_education,
            $applicant->last_education_year,
            $statusMap[$applicant->shortlisted] ?? 'Unknown',
            $applicant->created_at->format('Y-m-d H:i:s'),
            $resumeUrl
        ];
    }
}