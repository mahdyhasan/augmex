<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'invoice_date' => 'invoice_date',
    //     'work_start_date' => 'work_start_date',
    //     'work_end_date' => 'work_end_date',
    // ];

    protected $fillable = [
        'client_id', 'invoice_date', 'work_start_date', 'work_end_date', 'total_amount', 'invoice_no', 'status'
    ];

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with Invoice Items
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }
}
