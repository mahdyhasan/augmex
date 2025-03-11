<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'pay_period_start', 'pay_period_end', 'year', 'base_salary', 'bonuses', 'commission', 'transport', 'arrear', 'payment_status', 'payment_date'];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}