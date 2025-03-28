<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivanjSale extends Model
{
    use HasFactory;

    protected $table = 'divanj_sales';

    protected $fillable = [
        'employee_id',
        'date',
        'time',
        'name',
        'quantity',
        'price',
        'total',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
