<?php

// app/Repositories/NoteRepository.php
namespace App\Repositories;

use App\Models\Note;
use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Collection;

class NoteRepository
{
    protected $model;

    public function __construct(Note $note)
    {
        $this->model = $note;
    }

    public function create(array $data): Note
    {
        return $this->model->create($data);
    }

    public function getByResident(int $residentId, NoteType|string|null $type = null): Collection
    {
        $query = $this->model->where('resident_id', $residentId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getPaymentNotes(int $residentId): Collection
    {
        return $this->getByResident($residentId, NoteType::PAYMENT);
    }

    public function getLatestNotes(int $limit = 5): Collection
    {
        return $this->model->with('resident')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }


    public function formatNoteForDisplay(array $noteArray): string
    {
        $type = $noteArray['type'] ?? 'other';
        $noteText = $noteArray['note'] ?? '';
        
        // اگر نوع end_date است، فقط ماه و روز را نمایش بده
        if ($type === 'end_date' && preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $noteText, $matches)) {
            $noteText = $matches[2] . '/' . $matches[3];
        }
        
        return $this->cleanNoteText($noteText);
    }

    /**
     * دریافت رنگ badge بر اساس نوع نوت
     */
    public function getNoteBadgeColor(string $type): string
    {
        $colors = [
            'payment' => 'primary',      // آبی پررنگ
            'end_date' => 'info',        // آبی روشن
            'exit' => 'warning',         // زرد/نارنجی
            'demand' => 'danger',        // قرمز
            'other' => 'secondary'       // خاکستری
        ];

        return $colors[$type] ?? 'secondary';
    }

    /**
     * دریافت رنگ background برای badge (طیف آبی)
     */
    public function getNoteBadgeStyle(string $type): string
    {
        $styles = [
            'payment' => 'background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;',
            'end_date' => 'background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: white;',
            'exit' => 'background: linear-gradient(135deg, #60a5fa 0%, #93c5fd 100%); color: white;',
            'demand' => 'background: linear-gradient(135deg, #93c5fd 0%, #bfdbfe 100%); color: #1e3a8a;',
            'other' => 'background: linear-gradient(135deg, #bfdbfe 0%, #dbeafe 100%); color: #1e3a8a;'
        ];

        return $styles[$type] ?? $styles['other'];
    }

    /**
     * پاکسازی متن یادداشت برای نمایش بهتر
     */
    private function cleanNoteText(string $noteText): string
    {
        // حذف فاصله‌های اضافه
        $cleaned = trim($noteText);

        // تبدیل اعداد فارسی به انگلیسی (در صورت نیاز)
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $cleaned = str_replace($persianNumbers, $englishNumbers, $cleaned);

        return $cleaned;
    }
}
