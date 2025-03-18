<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSales extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'client_id', 'employee_id', 'date', 'sales_qty', 'sales_amount'
    ];

    // Relationship with Invoice
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


}
