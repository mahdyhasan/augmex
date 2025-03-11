<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'purchase_date', 'cost', 'depreciation_rate'];
    
    public function depreciationRecords()
    {
        return $this->hasMany(DepreciationRecord::class, 'asset_id');
    }
}