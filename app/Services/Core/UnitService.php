<?php

namespace App\Services\Core;

use App\Repositories\UnitRepository;
use App\Models\Unit;

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
                                return [
                                    'id' => $bed->id,
                                    'name' => $bed->name,
                                    'state' => $bed->state,
                                    'desc' => $bed->desc,
                                    'resident' => $bed->contracts->last()?->resident ? [
                                        'id' => $bed->contracts->last()->resident->id,
                                        'full_name' => $bed->contracts->last()->resident->full_name,
                                        'phone' => $bed->contracts->last()->resident->formatted_phone,
                                        'age' => $bed->contracts->last()->resident->age,
                                        'job' => $bed->contracts->last()->resident->job,
                                    ] : null,
                                ];
                            }),
                        ];
                    }),
                ];
            });
    }


}
