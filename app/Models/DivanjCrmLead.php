<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmLead extends Model
{
    protected $table = 'divanj_crm_leads';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name', 'mobile', 'landline', 'email', 'agent_id', 
        'source', 'note'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function callBackSheets()
    {
        return $this->hasMany(DivanjCrmCallBackSheet::class, 'lead_id');
    }

    public function callHistories()
    {
        return $this->hasMany(DivanjCrmCallHistory::class, 'lead_id');
    }

    public function followups()
    {
        return $this->hasMany(DivanjCrmFollowup::class, 'lead_id');
    }

    public function orderHistories()
    {
        return $this->hasMany(DivanjCrmOrderHistory::class, 'lead_id');
    }

    public function paymentDetails()
    {
        return $this->hasOne(DivanjCrmPaymentDetail::class, 'lead_id');
    }
}