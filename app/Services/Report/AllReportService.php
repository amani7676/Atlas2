<?php

namespace App\Services\Report;

use App\Repositories\{
    UnitRepository,
    RoomRepository,
    BedRepository,
    ContractRepository
};
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Support\Carbon;

class AllReportService
{
    public function __construct(
        protected UnitRepository     $unitRepo,
        protected RoomRepository     $roomRepo,
        protected BedRepository      $bedRepo,
        protected ContractRepository $contractRepo
    )
    {
    }

    /**
     * دریافت تمام ساکنان با جزئیات کامل (قرارداد، تخت، یادداشت‌ها، اتاق، واحد)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    /**
     * تمام ساکنین را با جزئیات کامل و قابلیت مرتب‌سازی داینامیک دریافت می‌کند.
     *
     * @param string $sortBy کلید مرتب‌سازی با استفاده از "dot notation" (مثال: 'resident.full_name').
     * @param string $sortDirection جهت مرتب‌سازی ('asc' یا 'desc').
     * @return array
     */
    /**
     * پاک کردن cache مربوط به residents
     */
    public static function clearResidentsCache(): void
    {
        // پاک کردن cache با استفاده از tags (اگر cache driver از tags پشتیبانی کند)
        try {
            if (method_exists(\Cache::getStore(), 'tags')) {
                \Cache::tags(['residents'])->flush();
            } else {
                // اگر tags پشتیبانی نمی‌شود، تمام cache را پاک می‌کنیم
                // یا می‌توانیم فقط key های خاص را پاک کنیم
                \Cache::flush();
            }
        } catch (\Exception $e) {
            // در صورت خطا، تمام cache را پاک می‌کنیم
            \Cache::flush();
        }
    }

    public function getAllResidentsWithDetails($sortBy = 'contract.day_since_payment', $sortDirection = 'desc')
    {
        // Cache key based on sort parameters
        $cacheKey = 'residents_with_details_' . md5($sortBy . $sortDirection);
        
        // در development از cache استفاده نکنیم یا مدت زمان را کاهش دهیم
        $cacheTime = app()->environment('production') ? 60 : 0;
        
        try {
            if ($cacheTime > 0) {
                return \Cache::tags(['residents'])->remember($cacheKey, $cacheTime, function () use ($sortBy, $sortDirection) {
                    return $this->fetchResidentsData($sortBy, $sortDirection);
                });
            } else {
                // بدون cache در development
                return $this->fetchResidentsData($sortBy, $sortDirection);
            }
        } catch (\Exception $e) {
            // اگر cache tags پشتیبانی نمی‌شود، بدون tags استفاده می‌کنیم
            if ($cacheTime > 0) {
                return \Cache::remember($cacheKey, $cacheTime, function () use ($sortBy, $sortDirection) {
                    return $this->fetchResidentsData($sortBy, $sortDirection);
                });
            } else {
                return $this->fetchResidentsData($sortBy, $sortDirection);
            }
        }
    }

    private function fetchResidentsData($sortBy, $sortDirection)
    {
        // تعیین اینکه آیا مرتب‌سازی نزولی است یا صعودی
        $isDescending = ($sortDirection === 'desc');

        return Resident::with([
            'contract.bed.room.unit', // زنجیره روابط را می‌توان به صورت بهینه‌تر نوشت
            'notes'
        ])
            ->get()
            ->map(function ($resident) use ($isDescending) {
                $contract = $resident->contract;

                return [
                    'resident' => [
                        'id' => $resident->id,
                        'full_name' => $resident->full_name,
                        'phone' => $resident->formatted_phone,
                        'age' => $resident->age,
                        'job' => $resident->job,
                        'referral_source' => $resident->referral_source,
                        'document' => $resident->document,
                        'form' => $resident->form,
                        'rent' => $resident->rent,
                        'trust' => $resident->trust,
                    ],
                    'contract' => $contract ? [
                        'id' => $contract->id,
                        'payment_date' => $contract->payment_date_jalali,
                        'day_since_payment' => $this->getDaysSincePayment($contract->payment_date),
                        'start_date' => $contract->start_date_jalali,
                        'end_date' => $contract->end_date_jalali,
                        'state' => $contract->state,
                    ] : null,
                    'bed' => $contract?->bed ? [
                        'id' => $contract->bed->id,
                        'name' => $contract->bed->name,
                        'state_ratio_resident' => $contract->bed->state_ratio_resident,
                        'state' => $contract->bed->state,
                        'desc' => $contract->bed->desc,
                    ] : null,
                    'room' => $contract?->bed?->room ? [
                        'id' => $contract->bed->room->id,
                        'name' => $contract->bed->room->name,
                        'bed_count' => $contract->bed->room->bed_count,
                        'desc' => $contract->bed->room->desc,
                    ] : null,
                    'unit' => $contract?->bed?->room?->unit ? [
                        'id' => $contract->bed->room->unit->id,
                        'name' => $contract->bed->room->unit->name,
                        'code' => $contract->bed->room->unit->code,
                        'desc' => $contract->bed->room->unit->desc,
                    ] : null,
                    'notes' => $resident->notes->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'type' => $note->type,
                            'note' => $note->note,
                            'created_at' => $note->created_at,
                        ];
                    }),
                ];
            })
            // مرتب‌سازی داینامیک با استفاده از پارامترها
            ->sortBy(function ($item) use ($sortBy, $isDescending) {
                // با data_get به راحتی به مقادیر تودرتو دسترسی پیدا می‌کنیم
                $value = data_get($item, $sortBy);

                // این منطق تضمین می‌کند که مقادیر null همیشه در انتهای لیست قرار گیرند
                if ($value === null) {
                    return $isDescending ? -INF : INF;
                }
                return $value;
            }, SORT_REGULAR, $isDescending)
            ->values()
            ->all();
    }

    /**
     * دریافت تمام ساکنان با جزئیات کامل (نسخه ساده‌تر)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllResidentsSimple()
    {
        return Resident::with([
            'contracts.bed.room.unit',
            'notes'
        ])->get();
    }

    /**
     * دریافت ساکنان فعال با جزئیات
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveResidentsWithDetails()
    {
        return Resident::whereHas('contract', function ($query) {
            $query->where('state', 1) // فرض بر این که state=1 یعنی فعال
            ->where('end_date', '>=', now())
                ->orWhereNull('end_date');
        })->with([
            'contracts' => function ($query) {
                $query->where('state', 1)
                    ->where('end_date', '>=', now())
                    ->orWhereNull('end_date')
                    ->with('bed.room.unit');
            },
            'notes'
        ])->get();
    }

    public function getDaysSincePayment($paymentDate)
    {
        $today = Carbon::now()->startOfDay();
        $payment = Carbon::parse($paymentDate)->startOfDay();

        $diff = $payment->diffInDays($today);

        return $payment->lessThan($today) ? -$diff : $diff;
    }


    public function getUnitWithDependence()
    {
        return Unit::with([
            'rooms' => function ($query) {
                $query->where('type', 'room') // Add this to filter rooms by type
                ->orderBy('name', 'desc')
                    ->with([
                        'beds' => function ($bedQuery) {
                            $bedQuery->with([
                                'contracts' => function ($contractQuery) {
                                    $contractQuery->with('resident.notes');
                                }
                            ]);
                        }
                    ]);
            }
        ])
            ->orderByDesc('code') // تغییر اصلی اینجا - مرتب سازی نزولی بر اساس کد
            ->get()->map(function ($unit) {
                return [
                    'unit' => [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'desc' => $unit->desc,
                        'color' => $unit->color ?? '#667eea',
                    ],
                    'rooms' => $unit->rooms->map(function ($room) {
                        return [
                            'room' => [
                                'id' => $room->id,
                                'name' => $room->name,
                                'bed_count' => $room->bed_count,
                                'desc' => $room->desc,
                                'color' => $room->color ?? '#f093fb',
                            ],
                            'beds' => $room->beds->map(function ($bed) {
                                return [
                                    'bed' => [
                                        'id' => $bed->id,
                                        'name' => $bed->name,
                                        'state_ratio_resident' => $bed->state_ratio_resident,
                                        'state' => $bed->state,
                                        'desc' => $bed->desc,
                                    ],
                                    'contracts' => $bed->contracts->map(function ($contract) {
                                        return [
                                            'contract' => [
                                                'id' => $contract->id,
                                                'payment_date' => $contract->payment_date_jalali,
                                                'day_since_payment' => $this->getDaysSincePayment($contract->payment_date),
                                                'start_date' => $contract->start_date_jalali,
                                                'end_date' => $contract->end_date_jalali,
                                                'state' => $contract->state,
                                            ],
                                            'resident' => $contract->resident ? [
                                                'id' => $contract->resident->id,
                                                'full_name' => $contract->resident->full_name,
                                                'phone' => $contract->resident->formatted_phone,
                                                'age' => $contract->resident->age,
                                                'job' => $contract->resident->job,
                                                'referral_source' => $contract->resident->referral_source,
                                                'document' => $contract->resident->document,
                                                'form' => $contract->resident->form,
                                                'rent' => $contract->resident->rent,
                                                'trust' => $contract->resident->trust,
                                            ] : null,
                                            'notes' => $contract->resident?->notes->map(function ($note) {
                                                    return [
                                                        'id' => $note->id,
                                                        'type' => $note->type,
                                                        'note' => $note->note,
                                                        'created_at' => $note->created_at,
                                                    ];
                                                }) ?? collect(),
                                        ];
                                    }),
                                ];
                            }),
                        ];
                    }),
                ];
            });
    }
}
