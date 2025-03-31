<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'pay_period_start', 'pay_period_end', 'year', 'base_salary', 'bonuses', 'commission', 'transport', 'others', 'payment_status', 'payment_date', 'deductions', 'net_salary'];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'datetime', 
    ];
    
}