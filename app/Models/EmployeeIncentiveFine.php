<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIncentiveFine extends Model
{
    use HasFactory;

    
    protected $fillable = ['employee_id', 'incentive_fine_type_id', 'date', 'amount', 'notes'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function type()
    {
        return $this->belongsTo(IncentiveFineType::class, 'incentive_fine_type_id');
    }

    
}
