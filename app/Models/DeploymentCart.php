<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'deployment_id',
        'inventory_id',
        'component',
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

    // Automatically populate component when saving
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deploymentCart) {
            if (empty($deploymentCart->component) && $deploymentCart->inventory_id) {
                $inventory = Inventory::find($deploymentCart->inventory_id);
                if ($inventory) {
                    $deploymentCart->component = $inventory->component;
                }
            }
        });

        static::updating(function ($deploymentCart) {
            if (empty($deploymentCart->component) && $deploymentCart->inventory_id) {
                $inventory = Inventory::find($deploymentCart->inventory_id);
                if ($inventory) {
                    $deploymentCart->component = $inventory->component;
                }
            }
        });
    }
}