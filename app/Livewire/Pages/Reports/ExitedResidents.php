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

    // Sorting
    public $sortBy = 'deleted_at';
    public $sortDirection = 'desc';

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
        // دریافت اقامتگران از جدول archive_data
        $archivedResidents = \App\Models\ArchiveData::all()
            ->map(function ($archive) {
                // فرمت کردن شماره تلفن
                $phone = $archive->phone;
                if ($phone) {
                    $phone = preg_replace('/^(\d{4})(\d{3})(\d{4})$/', '$1-$2-$3', $phone);
                }
                
                // تبدیل تاریخ‌ها به شمسی
                $paymentDateJalali = null;
                if ($archive->payment_date) {
                    try {
                        $paymentDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($archive->payment_date)->format('Y/m/d');
                    } catch (\Exception $e) {}
                }
                
                $startDateJalali = null;
                if ($archive->start_date) {
                    try {
                        $startDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($archive->start_date)->format('Y/m/d');
                    } catch (\Exception $e) {}
                }
                
                $endDateJalali = null;
                if ($archive->end_date) {
                    try {
                        $endDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($archive->end_date)->format('Y/m/d');
                    } catch (\Exception $e) {}
                }
                
                $archivedAtJalali = null;
                if ($archive->archived_at) {
                    try {
                        $archivedAtJalali = \Morilog\Jalali\Jalalian::fromDateTime($archive->archived_at)->format('Y/m/d');
                    } catch (\Exception $e) {}
                }
                
                return [
                    'resident' => [
                        'id' => $archive->id,
                        'full_name' => $archive->full_name,
                        'phone' => $phone,
                        'age' => $archive->age,
                        'job' => $archive->job,
                        'referral_source' => $archive->referral_source,
                        'document' => $archive->document,
                        'form' => $archive->form,
                        'rent' => $archive->rent,
                        'trust' => $archive->trust,
                        'deleted_at' => $archivedAtJalali,
                    ],
                    'contract' => [
                        'id' => null,
                        'payment_date' => $paymentDateJalali,
                        'day_since_payment' => $archive->payment_date ? $this->getAllReportService()->getDaysSincePayment($archive->payment_date) : null,
                        'start_date' => $startDateJalali,
                        'end_date' => $endDateJalali,
                        'state' => $archive->state,
                        'deleted_at' => $archivedAtJalali,
                    ],
                    'bed' => [
                        'id' => $archive->bed_id,
                        'name' => $archive->bed_name,
                        'state_ratio_resident' => null,
                        'state' => null,
                        'desc' => null,
                    ],
                    'room' => [
                        'id' => null,
                        'name' => $archive->room_name,
                        'bed_count' => null,
                        'desc' => null,
                    ],
                    'unit' => [
                        'id' => null,
                        'name' => $archive->unit_name,
                        'code' => null,
                        'desc' => null,
                    ],
                    'notes' => [],
                ];
            });

        // فیلتر بر اساس archived_at
        $residents = collect($archivedResidents)->filter(function ($data) {
            // تمام رکوردهای archive_data نمایش داده شوند
            return true;
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
                // archived_at را چک می‌کنیم
                if (isset($data['resident']['deleted_at']) && $data['resident']['deleted_at']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['resident']['deleted_at']);
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
                // archived_at را استفاده می‌کنیم
                $dateToCheck = null;
                if (isset($data['resident']['deleted_at']) && $data['resident']['deleted_at']) {
                    try {
                        $dateToCheck = $this->toMiladi($data['resident']['deleted_at']);
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

        // Apply sorting
        $residents = $residents->sortBy(function ($data) {
            $value = null;
            
            switch ($this->sortBy) {
                case 'start_date':
                    $value = $data['contract']['start_date'] ?? null;
                    break;
                case 'end_date':
                    $value = $data['contract']['end_date'] ?? null;
                    break;
                case 'deleted_at':
                    // Check both resident and contract deleted_at
                    $value = $data['resident']['deleted_at'] ?? $data['contract']['deleted_at'] ?? null;
                    break;
            }
            
            // Convert Jalali date to timestamp for sorting
            if ($value && $value !== '-') {
                try {
                    // Handle different date formats
                    if (preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $value)) {
                        return \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $value)->toCarbon()->timestamp;
                    }
                } catch (\Exception $e) {
                    // If parsing fails, return 0
                }
            }
            
            // Return a very old date for empty values so they appear at the end when sorting desc
            return 0;
        }, SORT_REGULAR, $this->sortDirection === 'asc' ? SORT_ASC : SORT_DESC);

        return $residents->values()->all();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc'; // Default to newest first
        }
    }

    public function deleteResident($archiveId)
    {
        try {
            // Find the archive record
            $archive = \App\Models\ArchiveData::find($archiveId);
            
            if (!$archive) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'خطا!',
                    'description' => 'رکورد آرشیو مورد نظر یافت نشد',
                    'timer' => 3000
                ]);
                return;
            }

            // Get resident name for confirmation message
            $residentName = $archive->full_name;

            // Delete the archive record
            $archive->delete();

            // Show success message
            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'حذف شد!',
                'description' => "رکورد آرشیو {$residentName} با موفقیت حذف شد",
                'timer' => 3000
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی در حذف رکورد آرشیو رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }


    public function render()
    {
        $residents = $this->getFilteredResidents();

        return view('livewire.pages.reports.exited-residents', [
            'residents' => $residents,
            'noteRepository' => $this->getNoteRepository(),
        ])->title('لیست اقامتگران');
    }
}

