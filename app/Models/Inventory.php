<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'supplier_id',
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Fix the category relationship
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }

    public function deploymentItems(): HasMany
    {
        return $this->hasMany(Deployment::class, 'inventory_id');
    }
}