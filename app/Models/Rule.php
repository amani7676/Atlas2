<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'display_order',
        'is_active',
        'rule_category_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'content' => 'string'
    ];

    public function category()
    {
        return $this->belongsTo(RuleCategory::class, 'rule_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('rule_category_id', $categoryId);
    }
}
