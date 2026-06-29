<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchiveData extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'phone',
        'age',
        'job',
        'referral_source',
        'form',
        'rent',
        'trust',
        'document',
        'birth_date',
        'payment_date',
        'bed_id',
        'state',
        'start_date',
        'end_date',
        'room_name',
        'bed_name',
        'unit_name',
        'archived_at',
    ];

    protected $casts = [
        'form' => 'boolean',
        'rent' => 'boolean',
        'trust' => 'boolean',
        'document' => 'boolean',
        'birth_date' => 'date',
        'payment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
