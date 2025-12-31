<?php

namespace App\Services\Core;

use App\Repositories\UnitRepository;
use App\Models\Unit;
use App\Models\Contract;
use App\Models\Note;

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
            'rooms.beds.contracts.resident.notes',
            'rooms.beds.contracts.bed.room.unit',
        ])
            ->get()
            ->flatMap(function ($unit) {
                $results = [];
                
                foreach ($unit->rooms as $room) {
                    foreach ($room->beds as $bed) {
                        foreach ($bed->contracts as $contract) {
                            $resident = $contract->resident;
                            
                            if ($resident) {
                                // استفاده از notes که قبلاً eager load شده
                                $residentNotes = $resident->notes ?? collect();
                                
                                // دریافت bed مربوط به این contract
                                $contractBed = $contract->bed;
                                $contractRoom = $contractBed ? $contractBed->room : null;
                                $contractUnit = $contractRoom ? $contractRoom->unit : null;
                                
                                // برای هر contract یک ردیف ایجاد می‌کنیم
                                $results[] = [
                                    // Resident ID و Contract ID در ابتدا
                                    'resident_id' => $resident->id,
                                    'contract_id' => $contract->id,
                                    
                                    // Unit Fields (مربوط به contract)
                                    'unit_id' => $contractUnit ? $contractUnit->id : null,
                                    'unit_name' => $contractUnit ? $contractUnit->name : null,
                                    'unit_code' => $contractUnit ? $contractUnit->code : null,
                                    'unit_desc' => $contractUnit ? $contractUnit->desc : null,
                                    'unit_created_at' => $contractUnit ? $contractUnit->created_at : null,
                                    'unit_updated_at' => $contractUnit ? $contractUnit->updated_at : null,
                                    
                                    // Room Fields (مربوط به contract)
                                    'room_id' => $contractRoom ? $contractRoom->id : null,
                                    'room_name' => $contractRoom ? $contractRoom->name : null,
                                    'room_code' => $contractRoom ? $contractRoom->code : null,
                                    'room_unit_id' => $contractRoom ? $contractRoom->unit_id : null,
                                    'room_bed_count' => $contractRoom ? $contractRoom->bed_count : null,
                                    'room_desc' => $contractRoom ? $contractRoom->desc : null,
                                    'room_type' => $contractRoom ? $contractRoom->type : null,
                                    'room_created_at' => $contractRoom ? $contractRoom->created_at : null,
                                    'room_updated_at' => $contractRoom ? $contractRoom->updated_at : null,
                                    
                                    // Bed Fields (مربوط به contract)
                                    'bed_id' => $contractBed ? $contractBed->id : null,
                                    'bed_name' => $contractBed ? $contractBed->name : null,
                                    'bed_code' => $contractBed ? $contractBed->code : null,
                                    'bed_room_id' => $contractBed ? $contractBed->room_id : null,
                                    'bed_state_ratio_resident' => $contractBed ? $contractBed->state_ratio_resident : null,
                                    'bed_state' => $contractBed ? $contractBed->state : null,
                                    'bed_desc' => $contractBed ? $contractBed->desc : null,
                                    'bed_created_at' => $contractBed ? $contractBed->created_at : null,
                                    'bed_updated_at' => $contractBed ? $contractBed->updated_at : null,
                                    
                                    // Contract Fields
                                    'contract_resident_id' => $contract->resident_id,
                                    'contract_payment_date' => $contract->payment_date,
                                    'contract_payment_date_jalali' => $contract->payment_date_jalali,
                                    'contract_bed_id' => $contract->bed_id,
                                    'contract_state' => $contract->state,
                                    'contract_start_date' => $contract->start_date,
                                    'contract_start_date_jalali' => $contract->start_date_jalali,
                                    'contract_end_date' => $contract->end_date,
                                    'contract_end_date_jalali' => $contract->end_date_jalali,
                                    'contract_created_at' => $contract->created_at,
                                    'contract_updated_at' => $contract->updated_at,
                                    'contract_deleted_at' => $contract->deleted_at,
                                    
                                    // Resident Fields
                                    'resident_full_name' => $resident->full_name,
                                    'resident_phone' => $resident->phone,
                                    'resident_age' => $resident->age,
                                    'resident_birth_date' => $resident->birth_date,
                                    'resident_job' => $resident->job,
                                    'resident_referral_source' => $resident->referral_source,
                                    'resident_form' => $resident->form,
                                    'resident_document' => $resident->document,
                                    'resident_rent' => $resident->rent,
                                    'resident_trust' => $resident->trust,
                                    'resident_created_at' => $resident->created_at,
                                    'resident_updated_at' => $resident->updated_at,
                                    'resident_deleted_at' => $resident->deleted_at,
                                    
                                    // Notes (تمام notes این resident)
                                    'notes' => $residentNotes->map(function ($note) {
                                        return [
                                            'note_id' => $note->id,
                                            'note_resident_id' => $note->resident_id,
                                            'note_type' => $note->type,
                                            'note_note' => $note->note,
                                            'note_created_at' => $note->created_at,
                                            'note_updated_at' => $note->updated_at,
                                            'note_deleted_at' => $note->deleted_at,
                                        ];
                                    })->values()->toArray(),
                                ];
                            }
                        }
                    }
                }
                
                return $results;
            })
            ->keyBy('resident_id');
    }

    public function getUnitsWithRoomsAndBeds()
    {
        return Unit::with([
            'rooms' => function ($query) {
                $query->whereHas('beds');
            },
            'rooms.beds'
        ])
            ->whereHas('rooms.beds')
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'code' => $unit->code,
                    'desc' => $unit->desc,
                    'created_at' => $unit->created_at,
                    'updated_at' => $unit->updated_at,
                    'rooms' => $unit->rooms->map(function ($room) {
                        return [
                            'id' => $room->id,
                            'name' => $room->name,
                            'code' => $room->code,
                            'unit_id' => $room->unit_id,
                            'bed_count' => $room->bed_count,
                            'desc' => $room->desc,
                            'type' => $room->type,
                            'created_at' => $room->created_at,
                            'updated_at' => $room->updated_at,
                            'beds' => $room->beds->map(function ($bed) {
                                return [
                                    'id' => $bed->id,
                                    'name' => $bed->name,
                                    'code' => $bed->code,
                                    'room_id' => $bed->room_id,
                                    'state_ratio_resident' => $bed->state_ratio_resident,
                                    'state' => $bed->state,
                                    'desc' => $bed->desc,
                                    'created_at' => $bed->created_at,
                                    'updated_at' => $bed->updated_at,
                                ];
                            })->values(),
                        ];
                    })->values(),
                ];
            })
            ->values();
    }


}
