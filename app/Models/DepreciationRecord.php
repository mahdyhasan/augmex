<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepreciationRecord extends Model
{
    use HasFactory;
    protected $fillable = ['asset_id', 'year', 'depreciation_amount'];
    
    public function asset()
    {
        return $this->belongsTo(FixedAsset::class);
    }
}