<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmPaymentDetail extends Model
{
    protected $table = 'divanj_crm_payment_details';
    protected $primaryKey = 'id';
    public $timestamps = true; // Changed to true to track updates

    protected $fillable = [
        'lead_id', 
        'cardboard', 
        'expiry_month', 
        'expiry_year',
        'sivivi',
        'card_type'
    ];
    
    protected $casts = [
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(DivanjCrmLead::class, 'lead_id');
    }
}