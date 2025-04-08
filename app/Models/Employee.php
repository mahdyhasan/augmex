<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon; 



class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_name', 
        'department', 
        'client_id', 
        'position', 
        'salary_amount', 
        'salary_type', 
        'login_time', 
        'user_id', 
        'date_of_hire', 
        'date_of_termination', 
        'date_of_birth', 
        'gender', 
        'married', 
        'nid_number', 
        'address_line_1', 
        'address_line_2', 
        'city', 
        'postal_code', 
        'country', 
        'emergency_contact_name', 
        'emergency_contact_relationship', 
        'emergency_contact_phone', 
        'resume_cv', 
        'notes'
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


    public function sales(): HasMany
    {
        return $this->hasMany(DivanjSale::class);
    }
    
    
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }



    public function getHireDateAttribute()
    {
        return $this->date_of_hire ? \Carbon\Carbon::parse($this->date_of_hire) : null;
    }



    public function getFilteredSales(Carbon $start, Carbon $end)
    {
        return $this->sales()
            ->whereBetween('date', [$start, $end])
            ->get();
    }
    


    // Scope for active employees
    public function scopeActiveForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId)
            ->whereNull('date_of_termination');
    }

    // Relationship with date filtering
    public function filteredSales(Carbon $start, Carbon $end): HasMany
    {
        return $this->hasMany(DivanjSale::class)
            ->whereBetween('date', [$start, $end]);
    }

}