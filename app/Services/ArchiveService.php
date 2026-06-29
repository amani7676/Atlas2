<?php

namespace App\Services;

use App\Models\ArchiveData;
use App\Models\Resident;
use App\Models\Contract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveService
{
    /**
     * Archive a resident and their contract data
     * Note: This method should be called within a DB transaction
     *
     * @param int $residentId
     * @return bool
     */
    public function archiveResident(int $residentId): bool
    {
        try {
            // Load resident with contract and related data
            $resident = Resident::with(['contract.bed.room.unit'])->find($residentId);

            if (!$resident) {
                Log::error("Resident not found for archiving: {$residentId}");
                return false;
            }

            $contract = $resident->contract;

            if (!$contract) {
                Log::error("Contract not found for resident: {$residentId}");
                return false;
            }

            // Create archive record
            $archiveData = ArchiveData::create([
                // Resident fields
                'full_name' => $resident->full_name,
                'phone' => $resident->phone,
                'age' => $resident->age,
                'job' => $resident->job,
                'referral_source' => $resident->referral_source,
                'form' => $resident->form,
                'rent' => $resident->rent,
                'trust' => $resident->trust,
                'document' => $resident->document,
                'birth_date' => $resident->birth_date,
                
                // Contract fields
                'payment_date' => $contract->payment_date,
                'bed_id' => $contract->bed_id,
                'state' => $contract->state,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                
                // Additional info
                'room_name' => $contract->bed->room->name ?? null,
                'bed_name' => $contract->bed->name ?? null,
                'unit_name' => $contract->bed->room->unit->name ?? null,
                
                // Archive timestamp
                'archived_at' => now(),
            ]);

            // Delete contract (hard delete)
            $contract->forceDelete();

            // Delete resident (hard delete)
            $resident->forceDelete();

            Log::info("Resident archived successfully: {$residentId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error archiving resident: {$residentId} - " . $e->getMessage());
            return false;
        }
    }
}
