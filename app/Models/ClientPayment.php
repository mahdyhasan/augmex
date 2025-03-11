<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'amount', 'payment_date', 'method'];
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}