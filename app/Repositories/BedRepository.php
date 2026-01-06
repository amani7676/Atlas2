<?php

// app/Repositories/BedRepository.php
namespace App\Repositories;

use App\Models\Bed;
use App\Enums\BedState;
use App\Enums\BedResidentState;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Collection;

class BedRepository
{
    protected $model;

    public function __construct(Bed $bed)
    {
        $this->model = $bed;
    }

    public function create(array $data): Bed
    {
        return $this->model->create($data);
    }

    /**
     * به‌روزرسانی اطلاعات تخت
     *
     * @param int $bedId
     * @param array $data
     * @return bool
     */
    public function update(int $bedId, array $data): bool
    {
        // یافتن تخت مورد نظر
        $bed = $this->model->findOrFail($bedId);

        // به‌روزرسانی فیلدها
        return $bed->update($data);
    }

    public function changeState(int $bedId, string $state): bool
    {
        return $this->model->where('id', $bedId)->update(['state' => $state]);
    }

    public function changeResidentState(int $bedId, string $state): bool
    {
        return $this->model->where('id', $bedId)
            ->update(['state_ratio_resident' => $state]);
    }

    public function getAvailableBeds(int $roomId): Collection
    {
        return $this->model->where('room_id', $roomId)
            ->where('state_ratio_resident', BedResidentState::EMPTY)
            ->where('state', BedState::ACTIVE)
            ->get();
    }

    public function countAll(): int
    {
        return $this->model->count();
    }

    public function getBedWithRelations(int $bedId)
    {
        return $this->model->with(['room.unit', 'contracts.resident'])->find($bedId);
    }

    public function getBeds(): Collection|array
    {
        // دریافت تمام تخت‌ها به همراه روابط
        return $this->model->with([
            'room.unit', // اتاق و واحد مربوطه
            'contracts.resident' // قراردادها و رزیدنت مربوطه
        ])->get();
    }

    /**
     * دریافت تخت‌های خالی و فعال
     *
     * @return Collection|array
     */
    public function getEmptyAndActiveBeds(): Collection|array
    {
        return $this->model->with([
            'room.unit', // اتاق و واحد مربوطه
            'contracts.resident' // قراردادها و رزیدنت مربوطه
        ])
        ->where('state', BedState::ACTIVE->value)
        ->where('state_ratio_resident', BedResidentState::EMPTY->value)
        ->whereHas('room') // اطمینان از اینکه اتاق وجود دارد
        ->get();
    }


    public function getBedWithResidentId($residentId)
    {

        $resident = Resident::with(['contracts.bed.room'])->find($residentId);

        if (!$resident || !$resident->contracts->first()) {
            return null;
        }

        $contract = $resident->contracts->first();
        $bed = $contract->bed;
        $room = $bed?->room;

        return [
            'resident_id' => $resident->id,
            'resident_name' => $resident->full_name,
            'bed_name' => $bed?->name,
            'room_name' => $room?->name,
        ];
        $resident = Resident::with(['contracts.bed.room'])->find($residentId);

        if (!$resident || !$resident->contracts->first()) {
            return null;
        }

        $contract = $resident->contracts->first();
        $bed = $contract->bed;
        $room = $bed?->room;

        return [
            'resident_id' => $resident->id,
            'resident_name' => $resident->full_name,
            'bed_name' => $bed?->name,
            'room_name' => $room?->name,
        ];
    }
}
