<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'name',
        'email',
        'phone',
        'age',
        'last_education',
        'last_education_institute',
        'last_education_year',
        'last_experience',
        'total_experience',
        'area',
        'resume_upload',
        'notes',
        'shortlisted'

    ];

    protected $casts = [
        'last_education_year' => 'integer',
        'age' => 'integer',
    ];

    // Accessor for resume URL
    public function getResumeUrlAttribute()
    {
        return asset('storage/' . $this->resume_upload);
    }
}