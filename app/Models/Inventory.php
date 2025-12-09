<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'component',
        'serial_num',
        'brand',
        'stock_qty',
        'date_added',
        'status',
        'supplier_id', // Changed from 'supplier'
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}