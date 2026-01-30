<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'body_id',
        'title',
        'body',
        'insert_date',
        'body_status',
        'description',
        'is_active',
    ];

    protected $casts = [
        'insert_date' => 'datetime',
        'is_active' => 'boolean',
    ];
}
