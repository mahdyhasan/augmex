<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['kdm', 'company', 'country', 'status', 'agency'];
    
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function clientConditions()
    {
        return $this->hasMany(ClientCondition::class);
    }

}