<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'field_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
