<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    protected $fillable = [
        'region_id',          
        'name',               
        'acronym',            
        'category',           // Matching database column name
        'certification_path', 
        'is_active', 
        'is_verified',         
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}