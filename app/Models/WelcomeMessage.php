<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelcomeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'body_id',
        'send_date',
        'is_active',
    ];

    protected $casts = [
        'send_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'body_id', 'body_id');
    }
}
