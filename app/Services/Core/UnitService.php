<?php

namespace App\Services\Core;

use App\Repositories\UnitRepository;
use App\Models\Unit;
use App\Models\Contract;

class UnitService
{
    public function __construct(
        protected UnitRepository $unitRepo
    )
    {
    }

    public function getAllUnits()
    {
        return $this->unitRepo->getAllWithRoomsCount();
    }

    public function createUnit(array $data): Unit
    {
        return $this->unitRepo->create([
            'name' => $data['name'],
            'code' => $data['code'],
            'desc' => $data['desc'] ?? null
        ]);
    }

    public function updateUnit(int $id, array $data): Unit
    {
        return $this->unitRepo->update($id, [
            'name' => $data['name'],
            'code' => $data['code'],
            'desc' => $data['desc'] ?? null
        ]);
    }

    public function getUnitWithRooms(int $unitId)
    {
        return $this->unitRepo->getWithRooms($unitId);
    }

    public function getUnitsWithDetails()
    {
        return Unit::with([
            'rooms.beds.contracts.resident', // بارگذاری روابط تا سطح Resident
        ])
            ->get()
            ->map(function ($unit) {
                return [
                    'unit' => [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'desc' => $unit->desc,
                    ],
                    'rooms' => $unit->rooms->map(function ($room) {
                        return [
                            'id' => $room->id,
                            'name' => $room->name,
                            'code' => $room->code,
                            'bed_count' => $room->bed_count,
                            'desc' => $room->desc,
                            'beds' => $room->beds->map(function ($bed) {
                                $lastContract = $bed->contracts->last();
                                $resident = $lastContract?->resident;
                                
                                return [
                                    'id' => $bed->id,
                                    'name' => $bed->name,
                                    'state' => $bed->state,
                                    'desc' => $bed->desc,
                                    'resident' => $resident ? [
                                        'id' => $resident->id,
                                        'full_name' => $resident->full_name,
                                        'phone' => $resident->phone,
                                        'formatted_phone' => $resident->formatted_phone,
                                        'age' => $resident->age,
                                        'job' => $resident->job,
                                        'referral_source' => $resident->referral_source,
                                        'form' => $resident->form,
                                        'rent' => $resident->rent,
                                        'trust' => $resident->trust,
                                        'document' => $resident->document,
                                        'birth_date' => $resident->birth_date,
                                        'contracts' => Contract::where('resident_id', $resident->id)
                                            ->with('bed')
                                            ->get()
                                            ->map(function ($contract) {
                                                return [
                                                    'id' => $contract->id,
                                                    'resident_id' => $contract->resident_id,
                                                    'bed_id' => $contract->bed_id,
                                                    'state' => $contract->state,
                                                    'payment_date' => $contract->payment_date,
                                                    'payment_date_jalali' => $contract->payment_date_jalali,
                                                    'start_date' => $contract->start_date,
                                                    'start_date_jalali' => $contract->start_date_jalali,
                                                    'end_date' => $contract->end_date,
                                                    'end_date_jalali' => $contract->end_date_jalali,
                                                ];
                                            })->values(),
                                    ] : null,
                                ];
                            }),
                        ];
                    }),
                ];
            });
    }


}
