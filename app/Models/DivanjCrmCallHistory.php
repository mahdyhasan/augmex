<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmCallHistory extends Model
{
    protected $table = 'divanj_crm_call_history';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'agent_id', 'medium', 'call_status_id', 'comment'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(DivanjCrmLead::class, 'lead_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function callStatus()
    {
        return $this->belongsTo(DivanjCrmCallStatus::class, 'call_status_id');
    }
}