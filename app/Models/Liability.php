<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liability extends Model
{
    use HasFactory;
    protected $fillable = ['account_id', 'amount', 'interest_rate', 'start_date', 'due_date', 'status'];
    
    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }
}
