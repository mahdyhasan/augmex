<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date_of_birth',
        'gender',
        'married',
        'nationality',
        'nid_number',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'country',
        'date_of_hire',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'resume_cv',
        'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
