<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'address',
        'logo',
        'playstore_app_url',
    ];
}
