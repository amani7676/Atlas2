<?php

namespace App\Models;

use App\Events\ResidentCreated;
use App\Observers\ResidentObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
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
    ];
    protected $dates = ['deleted_at'];

    protected $casts = [
        'form' => 'boolean',
        'rent' => 'boolean',
        'trust' => 'boolean',
        'document' => 'boolean',
        'birth_date' => 'date',
    ];

    // Relations
    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    public function getFormattedPhoneAttribute()
    {
        return preg_replace('/^(\d{4})(\d{3})(\d{4})$/', '$1-$2-$3', $this->phone);
    }


    // Scopes اختیاری
    public function scopeAgeBetween(Builder $q, ?int $min, ?int $max): Builder
    {
        if (!is_null($min)) $q->where('age', '>=', $min);
        if (!is_null($max)) $q->where('age', '<=', $max);
        return $q;
    }

    public function scopeJobIn(Builder $q, array $jobs): Builder
    {
        return empty($jobs) ? $q : $q->whereIn('job', $jobs);
    }

    public function scopeReferralIn(Builder $q, array $refs): Builder
    {
        return empty($refs) ? $q : $q->whereIn('referral_source', $refs);
    }



    // Accessor برای محاسبه خودکار سن
    public function getAgeAttribute($value)
    {
        if ($this->birth_date) {
            return Carbon::parse($this->birth_date)->age;
        }
        return $value;
    }

    // Mutator برای محاسبه خودکار سن هنگام ذخیره
    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = $value;
        if ($value) {
            $this->attributes['age'] = Carbon::parse($value)->age;
        }
    }

    protected static function booted()
    {
        // No event dispatching needed
    }

}
