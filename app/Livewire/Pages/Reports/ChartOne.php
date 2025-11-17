<?php

namespace App\Livewire\Pages\Reports;

use App\Models\Resident;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Collection;

class ChartOne extends Component
{
    public $ageFilter = 'all';
    public $referralFilter = 'all';
    public $jobFilter = 'all';

    public $ageRanges = [
        'all' => 'همه',
        '16-19' => '16 تا 19 سال',
        '20-23' => '20 تا 23 سال',
        '24-27' => '24 تا 27 سال',
        '28-31' => '28 تا 31 سال',
        '32-35' => '32 تا 35 سال',
        '36-39' => '36 تا 39 سال',
        '40-43' => '40 تا 43 سال',
        '44-47' => '44 تا 47 سال',
        '48+' => 'بالای 48 سال'
    ];

    public $referralSources = [
        'all' => 'همه',
        'university_introduction' => 'معرفی دانشگاه',
        'university_website' => 'وب‌سایت دانشگاه',
        'google' => 'گوگل',
        'map' => 'نقشه',
        'khobinja_website' => 'وب‌سایت خوبینجا',
        'introducing_friends' => 'معرفی دوستان',
        'street' => 'خیابان',
        'divar' => 'دیوار',
        'other' => 'سایر'
    ];

    public $jobs = [
        'all' => 'همه',
        'daneshjo_dolati' => 'دانشجو دولتی',
        'daneshjo_azad' => 'دانشجو آزاد',
        'daneshjo_other' => 'دانشجو سایر',
        'karmand_dolat' => 'کارمند دولت',
        'karmand_shakhse' => 'کارمند شخصی',
        'azad' => 'آزاد',
        'nurse' => 'پرستار',
        'other' => 'سایر'
    ];

    // چارت سن - فقط بر اساس فیلتر سن
    public function getAgeData()
    {
        $query = Resident::query();

        // فقط فیلتر سن اعمال می‌شود
        if ($this->ageFilter !== 'all') {
            [$min, $max] = $this->parseAgeRange($this->ageFilter);
            if ($max) {
                $query->whereBetween('age', [$min, $max]);
            } else {
                $query->where('age', '>=', $min);
            }
        }

        $residents = $query->whereNotNull('age')->get();
        $ageGroups = [
            '16-19' => 0,
            '20-23' => 0,
            '24-27' => 0,
            '28-31' => 0,
            '32-35' => 0,
            '36-39' => 0,
            '40-43' => 0,
            '44-47' => 0,
            '48+' => 0
        ];

        foreach ($residents as $resident) {
            $age = $resident->age;
            if ($age >= 16 && $age <= 19) {
                $ageGroups['16-19']++;
            } elseif ($age >= 20 && $age <= 23) {
                $ageGroups['20-23']++;
            } elseif ($age >= 24 && $age <= 27) {
                $ageGroups['24-27']++;
            } elseif ($age >= 28 && $age <= 31) {
                $ageGroups['28-31']++;
            } elseif ($age >= 32 && $age <= 35) {
                $ageGroups['32-35']++;
            } elseif ($age >= 36 && $age <= 39) {
                $ageGroups['36-39']++;
            } elseif ($age >= 40 && $age <= 43) {
                $ageGroups['40-43']++;
            } elseif ($age >= 44 && $age <= 47) {
                $ageGroups['44-47']++;
            } elseif ($age >= 48) {
                $ageGroups['48+']++;
            }
        }

        return [
            'labels' => array_keys($ageGroups),
            'data' => array_values($ageGroups)
        ];
    }

    // چارت نحوه آشنایی - فقط بر اساس فیلتر نحوه آشنایی
    public function getReferralData()
    {
        $query = Resident::query();

        // فقط فیلتر نحوه آشنایی اعمال می‌شود
        if ($this->referralFilter !== 'all') {
            $query->where('referral_source', $this->referralFilter);
        }

        $data = $query->select('referral_source', DB::raw('count(*) as count'))
            ->whereNotNull('referral_source')
            ->groupBy('referral_source')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $item) {
            $labels[] = $this->referralSources[$item->referral_source] ?? $item->referral_source;
            $counts[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $counts
        ];
    }

    // چارت شغل - فقط بر اساس فیلتر شغل
    public function getJobData()
    {
        $query = Resident::query();

        // فقط فیلتر شغل اعمال می‌شود
        if ($this->jobFilter !== 'all') {
            $query->where('job', $this->jobFilter);
        }

        $data = $query->select('job', DB::raw('count(*) as count'))
            ->whereNotNull('job')
            ->groupBy('job')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($data as $item) {
            $labels[] = $this->jobs[$item->job] ?? $item->job;
            $counts[] = $item->count;
        }

        return [
            'labels' => $labels,
            'data' => $counts
        ];
    }

    // متد برای گرفتن تعداد کل ساکنین با اعمال فیلترها
    public function getTotalResidents()
    {
        $query = Resident::query();

        if ($this->ageFilter !== 'all') {
            [$min, $max] = $this->parseAgeRange($this->ageFilter);
            if ($max) {
                $query->whereBetween('age', [$min, $max]);
            } else {
                $query->where('age', '>=', $min);
            }
        }

        if ($this->referralFilter !== 'all') {
            $query->where('referral_source', $this->referralFilter);
        }

        if ($this->jobFilter !== 'all') {
            $query->where('job', $this->jobFilter);
        }

        return $query->count();
    }

    private function parseAgeRange($range)
    {
        switch ($range) {
            case '16-19':
                return [16, 19];
            case '20-23':
                return [20, 23];
            case '24-27':
                return [24, 27];
            case '28-31':
                return [28, 31];
            case '32-35':
                return [32, 35];
            case '36-39':
                return [36, 39];
            case '40-43':
                return [40, 43];
            case '44-47':
                return [44, 47];
            case '48+':
                return [48, null];
            default:
                return [0, null];
        }
    }

    // متد برای گرفتن آمار چارت سن
    public function getAgeStats()
    {
        $totalResidents = Resident::count();
        $residentsWithAge = Resident::whereNotNull('age')->count();

        return [
            'filled' => $residentsWithAge,
            'total' => $totalResidents,
            'percentage' => $totalResidents > 0 ? round(($residentsWithAge / $totalResidents) * 100, 1) : 0
        ];
    }

    // متد برای گرفتن آمار چارت نحوه آشنایی
    public function getReferralStats()
    {
        $totalResidents = Resident::count();
        $residentsWithReferral = Resident::whereNotNull('referral_source')->count();

        return [
            'filled' => $residentsWithReferral,
            'total' => $totalResidents,
            'percentage' => $totalResidents > 0 ? round(($residentsWithReferral / $totalResidents) * 100, 1) : 0
        ];
    }

    // متد برای گرفتن آمار چارت شغل
    public function getJobStats()
    {
        $totalResidents = Resident::count();
        $residentsWithJob = Resident::whereNotNull('job')->count();

        return [
            'filled' => $residentsWithJob,
            'total' => $totalResidents,
            'percentage' => $totalResidents > 0 ? round(($residentsWithJob / $totalResidents) * 100, 1) : 0
        ];
    }

    public function render()
    {
        return view('livewire.pages.reports.chart-one', [
            'ageData' => $this->getAgeData(),
            'referralData' => $this->getReferralData(),
            'jobData' => $this->getJobData(),
            'totalResidents' => $this->getTotalResidents(),
            'ageStats' => $this->getAgeStats(),
            'referralStats' => $this->getReferralStats(),
            'jobStats' => $this->getJobStats()
        ]);
    }
}
