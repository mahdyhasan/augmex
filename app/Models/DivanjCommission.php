<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivanjCommission extends Model
{
    use HasFactory;

    protected $table = 'divanj_commissions';


    protected $fillable = [
            'employee_id',
            'start_date',
            'end_date',
            'target',
            'achieved_qty',
            'weekday_sales_qty',
            'weekday_sales_amount',
            'weekend_sales_qty',
            'weekend_sales_amount',
            'commission_type',
            'commission_amount',
        ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
}
