<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Division::query()->withCount(['districts', 'zones'])->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $divisions = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Division::count(),
            'active' => Division::where('status', true)->count(),
            'inactive' => Division::where('status', false)->count(),
        ];

        return view('admin.pages.division.index', compact('divisions', 'stats', 'search', 'status'));
    }

    /**
     * Show the form for creating a new division.
     */
    public function create(): View
    {
        return view('admin.pages.division.create');
    }

    /**
     * Store a newly created division in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:divisions,name'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'Division name is required.',
            'name.unique' => 'This division name already exists.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;

        Division::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('New division added successfully!', 'Success');
        }

        return redirect()->route('admin.division.index');
    }

    /**
     * Show the form for editing the specified division.
     */
    public function edit(Division $division): View
    {
        return view('admin.pages.division.edit', compact('division'));
    }

    /**
     * Update the specified division in storage.
     */
    public function update(Request $request, Division $division): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:divisions,name,'.$division->id],
            'status' => ['nullable'],
        ], [
            'name.required' => 'Division name is required.',
            'name.unique' => 'This division name already exists.',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $division->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('Division updated successfully!', 'Success');
        }

        return redirect()->route('admin.division.index');
    }

    /**
     * Remove the specified division from storage.
     */
    public function destroy(Division $division): RedirectResponse
    {
        $division->delete();

        if (function_exists('toastr')) {
            toastr()->success('Division deleted successfully!', 'Success');
        }

        return redirect()->route('admin.division.index');
    }

    /**
     * Toggle active/inactive status of the division.
     */
    public function toggleStatus(Division $division): JsonResponse|RedirectResponse
    {
        $division->status = ! $division->status;
        $division->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $division->status,
                'message' => $division->status ? 'Division activated' : 'Division deactivated',
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success('Status changed successfully!', 'Success');
        }

        return back();
    }
}
