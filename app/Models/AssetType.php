<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get all assets of this type
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Scope for active asset types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
