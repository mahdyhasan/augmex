<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 

class DivanjSale extends Model
{
    use HasFactory;

    protected $table = 'divanj_sales';

    protected $casts = [
        'date' => 'date',
    ];

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



    public function scopeWeekend(Builder $query): Builder
    {
        return $query->where(function($q) {
            $q->whereRaw('DAYOFWEEK(date) = 1') // Sunday
              ->orWhereRaw('DAYOFWEEK(date) = 7'); // Saturday
        });
    }

    /**
     * Scope for sales between dates
     */
    public function scopeBetweenDates(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('date', [$start, $end]);
    }


}
