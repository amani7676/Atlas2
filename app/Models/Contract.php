<?php

namespace App\Models;

use App\Jobs\SendWelcomeMessageJob;
use App\Models\WelcomeMessage;
use App\Observers\ContractObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class Contract extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'resident_id',
        'payment_date',
        'bed_id',
        'state',
        'start_date',
        'end_date',
        'welcome_sent'
    ];
    protected $dates = ['deleted_at'];

    protected $casts = [
        'payment_date' => 'date:Y-m-d',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'welcome_sent' => 'boolean',
    ];

    // Relations
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    public function getPaymentDateJalaliAttribute()
    {
        return $this->payment_date
            ? Jalalian::fromDateTime($this->payment_date)->format('Y/m/d')
            : null;
    }

    public function getStartDateJalaliAttribute()
    {
        return $this->start_date
            ? Jalalian::fromDateTime($this->start_date)->format('Y/m/d')
            : null;
    }

    public function getEndDateJalaliAttribute()
    {
        return $this->end_date
            ? Jalalian::fromDateTime($this->end_date)->format('Y/m/d')
            : null;
    }

    protected static function booted()
    {
        static::created(function ($contract) {
            Log::info("Contract created: {$contract->id} for resident: {$contract->resident_id}");
        });

        static::updated(function ($contract) {
            Log::info("Contract updated: {$contract->id} for resident: {$contract->resident_id}");
        });
    }
}
