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
        'password',
        'user_type_id',
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
        return optional($this->userType)->role_name === 'superAdmin';
    }

    public function isHR()
    {
        return optional($this->userType)->role_name === 'hr';
    }

    public function isAccountant()
    {
        return optional($this->userType)->role_name === 'accountant';
    }

    public function isEmployee()
    {
        return optional($this->userType)->role_name === 'user';
    }

}
