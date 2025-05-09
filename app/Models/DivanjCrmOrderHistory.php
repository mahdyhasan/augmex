<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmOrderHistory extends Model
{
    protected $table = 'divanj_crm_order_history';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'order_no', 'order_date', 'item', 
        'price', 'qty', 'total'
    ];
    
    protected $casts = [
        'order_date' => 'date',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(DivanjCrmLead::class, 'lead_id');
    }
}