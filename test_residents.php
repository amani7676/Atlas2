<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$unitService = app(\App\Services\Core\UnitService::class);
$residents = $unitService->getUnitsWithDetails();

echo "=== Residents List (should NOT include unit ID 5) ===\n";
echo "Total residents found: " . $residents->count() . "\n\n";

$unitIdsInList = [];
foreach ($residents as $residentId => $residentData) {
    $unitId = $residentData['unit_id'];
    $unitName = $residentData['unit_name'];
    $residentName = $residentData['resident_full_name'];
    
    if (!in_array($unitId, $unitIdsInList)) {
        $unitIdsInList[] = $unitId;
        echo "Unit ID: {$unitId}, Unit Name: {$unitName}\n";
    }
}

echo "\n=== Unit IDs in residents list ===\n";
echo implode(', ', $unitIdsInList) . "\n";

if (in_array(5, $unitIdsInList)) {
    echo "\n❌ ERROR: Unit ID 5 (واحد 4) is still showing in residents list!\n";
} else {
    echo "\n✅ SUCCESS: Unit ID 5 (واحد 4) is correctly hidden from residents list!\n";
}
