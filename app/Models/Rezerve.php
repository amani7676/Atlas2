<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Rezerve extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'bed_id',
        'note',
        'priority'
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];

    // Relations
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function getCreatedAtJalaliAttribute()
    {
        return $this->created_at
            ? Jalalian::fromDateTime($this->created_at)->format('Y/m/d')
            : null;
    }

    public function getUpdatedAtJalaliAttribute()
    {
        return $this->updated_at
            ? Jalalian::fromDateTime($this->updated_at)->format('Y/m/d')
            : null;
    }
}
