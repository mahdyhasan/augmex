<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'employee_id',
        'status_id',
        'start_date',
        'end_date',
        'reason',
        'approved'
    ];
    
    protected $casts = [
        'approved' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function status()
    {
        return $this->belongsTo(AttendanceStatus::class);
    }
    
    

}