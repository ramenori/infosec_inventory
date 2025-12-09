<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'deployment_id',
        'inventory_id',
        'quantity',
    ];

    // Relationship with Deployment
    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    // Relationship with Inventory
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}