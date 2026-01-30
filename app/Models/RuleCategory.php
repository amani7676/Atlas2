<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    public function rules()
    {
        return $this->hasMany(Rule::class)->orderBy('display_order');
    }

    public function activeRules()
    {
        return $this->hasMany(Rule::class)->where('is_active', true)->orderBy('display_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
