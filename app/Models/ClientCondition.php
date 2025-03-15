<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCondition extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'rate', 'rate_type', 'invoice_type'];
    
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
