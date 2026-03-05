<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contactperson';
    
    protected $fillable = [
        'name',
        'contact_number',
        'address',
        'satellite_office'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function deployments()
    {
        return $this->hasMany(Deployment::class, 'contact_person_id');
    }
}