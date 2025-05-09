<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmFollowup extends Model
{
    protected $table = 'divanj_crm_followup';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'lead_id', 'agent_id', 'schedule_date', 'schedule_time', 
        'status', 'notes'
    ];
    
    protected $casts = [
        'schedule_date' => 'date',
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
}