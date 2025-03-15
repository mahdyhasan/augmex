<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['expense_date', 'category_id', 'description', 'amount', 'receipt'];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
