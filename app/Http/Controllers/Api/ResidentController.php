<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Core\UnitService;
use App\Services\Report\AllReportService;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    protected $unitService;

    public function __construct(UnitService   $unitService)
    {
        $this->unitService  = $unitService ;
    }

    public function index() { $units = $this->unitService->getUnitsWithDetails(); return response()->json($units); }
}
