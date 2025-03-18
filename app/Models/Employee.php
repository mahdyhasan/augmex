<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'position', 
        'department', 
        'salary_type', 
        'salary_amount', 
        'date_of_termination', 
        'user_id', 
        'client_id', 
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
        'notes',
        'login_time'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


    public function sales()
    {
        return $this->hasMany(EmployeeSales::class);
    }



}