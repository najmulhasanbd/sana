<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Get districts by division ID.
     */
    public function getDistricts(int $divisionId): JsonResponse
    {
        $districts = District::where('division_id', $divisionId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    /**
     * Get zones by district ID.
     */
    public function getZones(int $districtId): JsonResponse
    {
        $zones = Zone::where('district_id', $districtId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($zones);
    }
}
