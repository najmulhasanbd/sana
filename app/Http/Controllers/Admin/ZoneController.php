<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    /**
     * Display a listing of zones/upazilas.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $districtId = $request->query('district_id');
        $divisionId = $request->query('division_id');

        $query = Zone::query()->with(['district.division'])->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($districtId)) {
            $query->where('district_id', $districtId);
        } elseif (! empty($divisionId)) {
            $query->whereHas('district', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $zones = $query->paginate(15)->withQueryString();
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::active()->orderBy('name')->get();

        $stats = [
            'total' => Zone::count(),
            'active' => Zone::where('status', true)->count(),
            'inactive' => Zone::where('status', false)->count(),
        ];

        return view('admin.pages.zone.index', compact('zones', 'divisions', 'districts', 'stats', 'search', 'status', 'districtId', 'divisionId'));
    }

    /**
     * Show the form for creating a new zone.
     */
    public function create(): View
    {
        $districts = District::with('division')->active()->orderBy('name')->get();

        return view('admin.pages.zone.create', compact('districts'));
    }

    /**
     * Store a newly created zone in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['nullable'],
        ], [
            'district_id.required' => 'Please select a district.',
            'district_id.exists' => 'Selected district does not exist.',
            'name.required' => 'Zone/Upazila name is required.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;

        Zone::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('New zone/upazila added successfully!', 'Success');
        }

        return redirect()->route('admin.zone.index');
    }

    /**
     * Show the form for editing the specified zone.
     */
    public function edit(Zone $zone): View
    {
        $districts = District::with('division')->active()->orderBy('name')->get();

        return view('admin.pages.zone.edit', compact('zone', 'districts'));
    }

    /**
     * Update the specified zone in storage.
     */
    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['nullable'],
        ], [
            'district_id.required' => 'Please select a district.',
            'district_id.exists' => 'Selected district does not exist.',
            'name.required' => 'Zone/Upazila name is required.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $zone->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('Zone/Upazila updated successfully!', 'Success');
        }

        return redirect()->route('admin.zone.index');
    }

    /**
     * Remove the specified zone from storage.
     */
    public function destroy(Zone $zone): RedirectResponse
    {
        $zone->delete();

        if (function_exists('toastr')) {
            toastr()->success('Zone/Upazila deleted successfully!', 'Success');
        }

        return redirect()->route('admin.zone.index');
    }

    /**
     * Toggle active/inactive status of the zone.
     */
    public function toggleStatus(Zone $zone): JsonResponse|RedirectResponse
    {
        $zone->status = ! $zone->status;
        $zone->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $zone->status,
                'message' => $zone->status ? 'Zone activated' : 'Zone deactivated',
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success('Status changed successfully!', 'Success');
        }

        return back();
    }
}
