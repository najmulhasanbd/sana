<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    /**
     * Display a listing of districts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $divisionId = $request->query('division_id');

        $query = District::query()->with('division')->withCount('zones')->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($divisionId)) {
            $query->where('division_id', $divisionId);
        }

        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $districts = $query->paginate(15)->withQueryString();
        $divisions = Division::active()->orderBy('name')->get();

        $stats = [
            'total' => District::count(),
            'active' => District::where('status', true)->count(),
            'inactive' => District::where('status', false)->count(),
        ];

        return view('admin.pages.district.index', compact('districts', 'divisions', 'stats', 'search', 'status', 'divisionId'));
    }

    /**
     * Show the form for creating a new district.
     */
    public function create(): View
    {
        $divisions = Division::active()->orderBy('name')->get();

        return view('admin.pages.district.create', compact('divisions'));
    }

    /**
     * Store a newly created district in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['nullable'],
        ], [
            'division_id.required' => 'Please select a division.',
            'division_id.exists' => 'Selected division does not exist.',
            'name.required' => 'District name is required.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;

        District::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('New district added successfully!', 'Success');
        }

        return redirect()->route('admin.district.index');
    }

    /**
     * Show the form for editing the specified district.
     */
    public function edit(District $district): View
    {
        $divisions = Division::active()->orderBy('name')->get();

        return view('admin.pages.district.edit', compact('district', 'divisions'));
    }

    /**
     * Update the specified district in storage.
     */
    public function update(Request $request, District $district): RedirectResponse
    {
        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'name' => ['required', 'string', 'max:100'],
            'status' => ['nullable'],
        ], [
            'division_id.required' => 'Please select a division.',
            'division_id.exists' => 'Selected division does not exist.',
            'name.required' => 'District name is required.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $district->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('District updated successfully!', 'Success');
        }

        return redirect()->route('admin.district.index');
    }

    /**
     * Remove the specified district from storage.
     */
    public function destroy(District $district): RedirectResponse
    {
        $district->delete();

        if (function_exists('toastr')) {
            toastr()->success('District deleted successfully!', 'Success');
        }

        return redirect()->route('admin.district.index');
    }

    /**
     * Toggle active/inactive status of the district.
     */
    public function toggleStatus(District $district): JsonResponse|RedirectResponse
    {
        $district->status = ! $district->status;
        $district->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $district->status,
                'message' => $district->status ? 'District activated' : 'District deactivated',
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success('Status changed successfully!', 'Success');
        }

        return back();
    }
}
