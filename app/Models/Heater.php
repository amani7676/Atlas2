<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heater extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'number',
        'desc',
        'status',
        'model',
        'serial_number',
        'installation_date',
        'room_id'
    ];

    protected $casts = [
        'installation_date' => 'date'
    ];

    /**
     * Get the room that owns this heater
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if heater is active
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
     * Scope for active heaters
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
