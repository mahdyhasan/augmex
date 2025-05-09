<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'user_type_id',
        'status',
        'open_cart_token',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship: A User can have one Employee record
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    /**
     * Relationship: A user can be linked to an employee
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Role Checking Methods
     */
    public function isSuperAdmin()
    {
        return $this->user_type_id == 1; // SuperAdmin
    }

    public function isHR()
    {
        return $this->user_type_id == 3; // HR
    }

    public function isAccountant()
    {
        return $this->user_type_id == 2; // Accounts
    }

    public function isUser()
    {
        return $this->user_type_id == 4; // Normal User
    }




    public function callBackSheets()
    {
        return $this->hasMany(DivanjCrmCallBackSheet::class, 'agent_id');
    }

    public function callHistories()
    {
        return $this->hasMany(DivanjCrmCallHistory::class, 'agent_id');
    }

    public function followups()
    {
        return $this->hasMany(DivanjCrmFollowup::class, 'agent_id');
    }

    public function leads()
    {
        return $this->hasMany(DivanjCrmLead::class, 'agent_id');
    }

}
