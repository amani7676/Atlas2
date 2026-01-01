<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'unit_id',
        'bed_count',
        'desc',
        'color'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    // تعریف رابطه Many-to-Many با Cooler
    public function coolers()
    {
        return $this->belongsToMany(Cooler::class, 'cooler_room', 'room_id', 'cooler_id')
            ->withPivot('connection_type', 'connected_at', 'notes','id')
            ->withTimestamps();
    }

    public function keys()
    {
        return $this->hasMany(Key::class);
    }


    protected $casts = [
        'bed_count' => 'integer',
        'code' => 'integer'
    ];

    /**
     * Get connection details for a specific cooler
     */
    public function getConnectionDetails($coolerId)
    {
        return $this->coolers()->where('cooler_id', $coolerId)->first()?->pivot;
    }

    /**
     * Get active coolers connected to this room
     */
    public function activeCoolers()
    {
        return $this->coolers()->where('status', 'active');
    }

    /**
     * Get room full name with unit
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . $this->unit->name;
    }

    /**
     * Check if room has any coolers
     */
    public function hasCoolers()
    {
        return $this->coolers()->exists();
    }

    /**
     * Get room capacity status
     */
    public function getCapacityStatusAttribute()
    {
        $coolerCount = $this->coolers()->count();

        if ($coolerCount === 0) {
            return 'بدون کولر';
        } elseif ($coolerCount === 1) {
            return 'یک کولر';
        } else {
            return $coolerCount . ' کولر';
        }
    }

    /**
     * Scope for rooms in specific unit
     */
    public function scopeInUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /**
     * Scope for rooms with coolers
     */
    public function scopeWithCoolers($query)
    {
        return $query->has('coolers');
    }

    /**
     * Scope for rooms without coolers
     */
    public function scopeWithoutCoolers($query)
    {
        return $query->doesntHave('coolers');
    }

}
