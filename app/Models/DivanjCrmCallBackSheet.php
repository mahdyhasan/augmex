<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivanjCrmCallBackSheet extends Model
{
    protected $table = 'divanj_crm_call_back_sheet';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['lead_id', 'agent_id'];
    
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
}