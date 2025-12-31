<?php

namespace App\Livewire\Pages\Reports;

use App\Services\Report\AllReportService;
use App\Services\Core\StatusService;
use App\Repositories\NoteRepository;
use App\Models\Unit;
use App\Models\Room;
use App\Models\Bed;
use App\Traits\HasDateConversion;
use Livewire\Component;
use Carbon\Carbon;

class ExitedResidents extends Component
{
    use HasDateConversion;
    
    protected $allReportService;
    protected $statusService;
    protected $noteRepository;

    // فیلترها
    public $startDate = '';
    public $endDate = '';
    public $unitId = '';
    public $roomId = '';
    public $phone = '';
    public $name = '';
    public $nationalCode = '';

    // داده‌های فیلتر - باید به array تبدیل شوند
    public $units = [];
    public $rooms = [];

    public function mount(
        AllReportService $allReportService,
        StatusService $statusService,
        NoteRepository $noteRepository
    ): void {
        $this->allReportService = $allReportService;
        $this->statusService = $statusService;
        $this->noteRepository = $noteRepository;
        $this->loadFilterData();
    }

    protected function getAllReportService(): AllReportService
    {
        if (!$this->allReportService) {
            $this->allReportService = app(AllReportService::class);
        }
        return $this->allReportService;
    }

    protected function getStatusService(): StatusService
    {
        if (!$this->statusService) {
            $this->statusService = app(StatusService::class);
        }
        return $this->statusService;
    }

    protected function getNoteRepository(): NoteRepository
    {
        if (!$this->noteRepository) {
            $this->noteRepository = app(NoteRepository::class);
        }
        return $this->noteRepository;
    }

    public function loadFilterData(): void
    {
        $this->units = Unit::orderBy('name')->get()->toArray();
        $this->rooms = Room::where('type', 'room')->orderBy('name')->get()->toArray();
    }

    public function updatedUnitId($value): void
    {
        if ($value) {
            $this->rooms = Room::where('type', 'room')
                ->where('unit_id', $value)
                ->orderBy('name')
                ->get()
                ->toArray();
            // اگر واحد تغییر کرد، اتاق را پاک می‌کنیم
            if ($this->roomId) {
                $this->roomId = '';
            }
        } else {
            // اگر واحد پاک شد، همه اتاق‌ها را نمایش می‌دهیم
            $this->loadFilterData();
            $this->roomId = '';
        }
    }

    public function updatedRoomId($value): void
    {
        // فقط برای به‌روزرسانی rooms در صورت نیاز
    }

    public function updatedStartDate($value): void
    {
        // اگر تاریخ شروع انتخاب شد و تاریخ پایان هم وجود دارد، بررسی می‌کنیم
        if ($value && $this->endDate) {
            try {
                $startDateMiladi = $this->toMiladi($value);
                $endDateMiladi = $this->toMiladi($this->endDate);
                $startCarbon = Carbon::parse($startDateMiladi);
                $endCarbon = Carbon::parse($endDateMiladi);
                
                if ($startCarbon->greaterThan($endCarbon)) {
                    // اگر تاریخ شروع بعد از تاریخ پایان باشد، تاریخ پایان را پاک می‌کنیم
                    $this->endDate = '';
                    $this->dispatch('show-toast', [
                        'type' => 'warning',
                        'title' => 'هشدار',
                        'description' => 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد. تاریخ پایان پاک شد.',
                        'timer' => 3000
                    ]);
                }
            } catch (\Exception $e) {
                // در صورت خطا، کاری نمی‌کنیم
            }
        }
    }

    public function updatedEndDate($value): void
    {
        // اگر تاریخ پایان انتخاب شد و تاریخ شروع هم وجود دارد، بررسی می‌کنیم
        if ($value && $this->startDate) {
            try {
                $startDateMiladi = $this->toMiladi($this->startDate);
                $endDateMiladi = $this->toMiladi($value);
                $startCarbon = Carbon::parse($startDateMiladi);
                $endCarbon = Carbon::parse($endDateMiladi);
                
                if ($endCarbon->lessThan($startCarbon)) {
                    // اگر تاریخ پایان قبل از تاریخ شروع باشد، تاریخ پایان را پاک می‌کنیم
                    $this->endDate = '';
                    $this->dispatch('show-toast', [
                        'type' => 'warning',
                        'title' => 'هشدار',
                        'description' => 'تاریخ پایان نمی‌تواند قبل از تاریخ شروع باشد.',
                        'timer' => 3000
                    ]);
                }
            } catch (\Exception $e) {
                // در صورت خطا، کاری نمی‌کنیم
            }
        }
    }

    public function resetFilters(): void
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->unitId = '';
        $this->roomId = '';
        $this->phone = '';
        $this->name = '';
        $this->nationalCode = '';
        $this->loadFilterData();
    }

    public function getFilteredResidents()
    {
        // دریافت اقامتگران شامل deleted ones
        $residents = \App\Models\Resident::withTrashed()
            ->with([
                'contract' => function ($query) {
                    $query->withTrashed()->with(['bed.room.unit']);
                },
                'notes'
            ])
            ->get()
            ->map(function ($resident) {
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
                        'deleted_at' => $resident->deleted_at ? \Morilog\Jalali\Jalalian::fromDateTime($resident->deleted_at)->format('Y/m/d') : null,
                    ],
                    'contract' => $contract ? [
                        'id' => $contract->id,
                        'payment_date' => $contract->payment_date_jalali,
                        'day_since_payment' => $this->getAllReportService()->getDaysSincePayment($contract->payment_date),
                        'start_date' => $contract->start_date_jalali,
                        'end_date' => $contract->end_date_jalali,
                        'state' => $contract->state,
                        'deleted_at' => $contract->deleted_at ? \Morilog\Jalali\Jalalian::fromDateTime($contract->deleted_at)->format('Y/m/d') : null,
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
            });

        // فیلتر بر اساس state = 'leaving' یا deleted_at
        $residents = collect($residents)->filter(function ($data) {
            // اگر contract deleted شده باشد
            if ($data['contract'] && isset($data['contract']['deleted_at']) && $data['contract']['deleted_at']) {
                return true;
            }
            // اگر resident deleted شده باشد
            if (isset($data['resident']['deleted_at']) && $data['resident']['deleted_at']) {
                return true;
            }
            // اگر state = 'leaving' باشد
            return $data['contract'] && $data['contract']['state'] === 'leaving';
        });

        // تعیین تاریخ شروع و پایان برای فیلتر
        $startDateCarbon = null;
        $endDateCarbon = null;

        // اگر تاریخ شروع انتخاب نشده، از اولین تاریخ موجود استفاده می‌کنیم
        if ($this->startDate) {
            try {
                $startDateMiladi = $this->toMiladi($this->startDate);
                $startDateCarbon = Carbon::parse($startDateMiladi)->startOfDay();
            } catch (\Exception $e) {
                // در صورت خطا، از null استفاده می‌کنیم
            }
        } else {
            // پیدا کردن اولین تاریخ موجود (قدیمی‌ترین)
            $firstDate = null;
            foreach ($residents as $data) {
                $dateToCheck = null;
                // اول deleted_at را چک می‌کنیم
                if ($data['contract'] && isset($data['contract']['deleted_at']) && $data['contract']['deleted_at']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['contract']['deleted_at']);
                    } catch (\Exception $e) {
                        // اگر نتوانستیم، از end_date استفاده می‌کنیم
                        if ($data['contract']['end_date']) {
                            try {
                                $dateToCheck = $this->toMiladi($data['contract']['end_date']);
                            } catch (\Exception $e2) {
                                continue;
                            }
                        }
                    }
                } elseif ($data['contract'] && $data['contract']['end_date']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['contract']['end_date']);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                if ($dateToCheck && (!$firstDate || $dateToCheck < $firstDate)) {
                    $firstDate = $dateToCheck;
                }
            }
            
            if ($firstDate) {
                $startDateCarbon = Carbon::parse($firstDate)->startOfDay();
            }
        }

        // اگر تاریخ پایان انتخاب نشده، تا امروز استفاده می‌کنیم
        if ($this->endDate) {
            try {
                $endDateMiladi = $this->toMiladi($this->endDate);
                $endDateCarbon = Carbon::parse($endDateMiladi)->endOfDay();
            } catch (\Exception $e) {
                // در صورت خطا، تا امروز استفاده می‌کنیم
                $endDateCarbon = Carbon::now()->endOfDay();
            }
        } else {
            // تا امروز
            $endDateCarbon = Carbon::now()->endOfDay();
        }

        // اعمال فیلتر تاریخ
        if ($startDateCarbon || $endDateCarbon) {
            $residents = $residents->filter(function ($data) use ($startDateCarbon, $endDateCarbon) {
                // اگر contract deleted شده باشد، از deleted_at استفاده می‌کنیم
                $dateToCheck = null;
                if ($data['contract'] && isset($data['contract']['deleted_at']) && $data['contract']['deleted_at']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['contract']['deleted_at']);
                    } catch (\Exception $e) {
                        // اگر نتوانستیم تبدیل کنیم، از end_date استفاده می‌کنیم
                        if ($data['contract']['end_date']) {
                            try {
                                $dateToCheck = $this->toMiladi($data['contract']['end_date']);
                            } catch (\Exception $e2) {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                } elseif ($data['contract'] && $data['contract']['end_date']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['contract']['end_date']);
                    } catch (\Exception $e) {
                        return false;
                    }
                } else {
                    return false;
                }
                
                if (!$dateToCheck) {
                    return false;
                }
                
                $dateCarbon = Carbon::parse($dateToCheck)->startOfDay();
                
                // بررسی تاریخ شروع
                if ($startDateCarbon && $dateCarbon->lessThan($startDateCarbon)) {
                    return false;
                }
                
                // بررسی تاریخ پایان
                if ($endDateCarbon && $dateCarbon->greaterThan($endDateCarbon)) {
                    return false;
                }
                
                return true;
            });
        }

        // فیلتر بر اساس واحد
        if ($this->unitId) {
            $residents = $residents->filter(function ($data) {
                if (!$data['unit']) {
                    return false;
                }
                // تبدیل به string برای مقایسه دقیق
                return (string)$data['unit']['id'] === (string)$this->unitId;
            });
        }

        // فیلتر بر اساس اتاق
        if ($this->roomId) {
            $residents = $residents->filter(function ($data) {
                if (!$data['room']) {
                    return false;
                }
                // تبدیل به string برای مقایسه دقیق
                return (string)$data['room']['id'] === (string)$this->roomId;
            });
        }

        // فیلتر بر اساس تلفن
        if ($this->phone) {
            $residents = $residents->filter(function ($data) {
                return $data['resident']['phone'] && 
                       stripos($data['resident']['phone'], $this->phone) !== false;
            });
        }

        // فیلتر بر اساس نام
        if ($this->name) {
            $residents = $residents->filter(function ($data) {
                return $data['resident']['full_name'] && 
                       stripos($data['resident']['full_name'], $this->name) !== false;
            });
        }

        // فیلتر بر اساس کد ملی
        if ($this->nationalCode) {
            $residents = $residents->filter(function ($data) {
                // فرض می‌کنیم کد ملی در document ذخیره می‌شود
                return $data['resident']['document'] && 
                       stripos($data['resident']['document'], $this->nationalCode) !== false;
            });
        }

        return $residents->values()->all();
    }


    public function render()
    {
        $residents = $this->getFilteredResidents();

        return view('livewire.pages.reports.exited-residents', [
            'residents' => $residents,
            'noteRepository' => $this->getNoteRepository(),
        ])->title('اقامتگران خروجی');
    }
}

