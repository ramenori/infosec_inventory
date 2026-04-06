<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'component',
        'details',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get logs for current user
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Scope to filter by date
     */
    public function scopeByDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('created_at', $date);
        }
        return $query;
    }

    /**
     * Scope to filter by entity type
     */
    public function scopeByEntityType($query, $type)
    {
        return $query->where('entity_type', $type);
    }
}
