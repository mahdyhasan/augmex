<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCondition extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'rate','currency', 'rate_type', 'invoice_type'];
    
    public function client()
    {
        return $this->hasMany(Client::class);
    }



}
