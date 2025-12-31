<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Core\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        $units = $this->unitService->getUnitsWithRoomsAndBeds();
        return response()->json($units);
    }
}


