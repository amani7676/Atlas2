<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_type_id',
        'name',
        'number',
        'description',
        'status',
        'model',
        'serial_number',
        'installation_date',
        'notes'
    ];

    protected $casts = [
        'installation_date' => 'date'
    ];

    /**
     * Get the asset type
     */
    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    /**
     * Get all rooms connected to this asset
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'asset_room')
            ->withPivot(['connection_type', 'connected_at', 'notes', 'id'])
            ->withTimestamps();
    }

    /**
     * Get connection details for a specific room
     */
    public function getConnectionDetails($roomId)
    {
        return $this->rooms()->where('room_id', $roomId)->first()?->pivot;
    }

    /**
     * Check if asset is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            'maintenance' => 'تعمیرات',
            default => 'نامشخص'
        };
    }

    /**
     * Scope for active assets
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific asset type
     */
    public function scopeOfType($query, $typeId)
    {
        return $query->where('asset_type_id', $typeId);
    }
}
