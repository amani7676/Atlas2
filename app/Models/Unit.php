<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
     protected $fillable = [
        'name',
        'code',
        'desc',
        'color'
    ];

    // Relations
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function coolers()
    {
        return $this->hasManyThrough(
            Cooler::class,
            Room::class,
            'unit_id', // Foreign key on rooms table
            'id', // Foreign key on coolers table
            'id', // Local key on units table
            'id' // Local key on rooms table
        )->distinct();
    }

    public function getTotalBedsAttribute()
    {
        return $this->rooms()->sum('bed_count');
    }

    /**
     * Get rooms count in this unit
     */
    public function getRoomsCountAttribute()
    {
        return $this->rooms()->count();
    }

    /**
     * Get connected coolers count in this unit
     */
    public function getCoolersCountAttribute()
    {
        return $this->rooms()->withCoolers()->count();
    }

    /**
     * Scope for units with rooms
     */
    public function scopeWithRooms($query)
    {
        return $query->has('rooms');
    }


}
