<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'deployed_to',
        'department',
        'deployment_date',
        'remarks',
        'status',
    ];

    protected $casts = [
        'deployment_date' => 'date',
    ];

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Deployment Items
    public function items(): HasMany
    {
        return $this->hasMany(DeploymentItem::class);
    }

    // Generate reference number
    public static function generateReferenceNumber(): string
    {
        return 'DEP-' . date('Ymd') . '-' . str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}