<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'status_id', 'is_weekend', 'leave_status', 'isLate'];

    public $timestamps = false;

    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function status()
    {
        return $this->belongsTo(AttendanceStatus::class, 'status_id', 'id');
    }


    
    
}