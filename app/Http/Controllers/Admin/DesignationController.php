<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations (Single page with modals for Create & Edit).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Designation::query()->withCount('employees')->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        $designations = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Designation::count(),
            'active' => Designation::where('status', true)->count(),
            'inactive' => Designation::where('status', false)->count(),
        ];

        return view('admin.pages.employee.designation.index', compact('designations', 'stats', 'search'));
    }

    /**
     * Store a newly created designation in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:designations,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'পদবী বা কাজের ধরনের নাম প্রদান করুন।',
            'name.unique' => 'এই পদবীটি ইতিমধ্যে তালিকায় রয়েছে।',
        ]);

        $validated['status'] = true;

        $designation = Designation::create($validated);

        if (function_exists('toastr')) {
            toastr()->success("নতুন পদবী '{$designation->name}' সফলভাবে যোগ করা হয়েছে!", 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন পদবী সফলভাবে যোগ করা হয়েছে!',
                'data' => $designation,
            ]);
        }

        return redirect()->route('admin.designation.index');
    }

    /**
     * Update the specified designation in storage.
     */
    public function update(Request $request, Designation $designation): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:designations,name,'.$designation->id],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'পদবী বা কাজের ধরনের নাম প্রদান করুন।',
            'name.unique' => 'এই পদবীটি ইতিমধ্যে তালিকায় রয়েছে।',
        ]);

        $designation->update($validated);

        if (function_exists('toastr')) {
            toastr()->success("পদবী '{$designation->name}' সফলভাবে আপডেট করা হয়েছে!", 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'পদবী সফলভাবে আপডেট করা হয়েছে!',
                'data' => $designation,
            ]);
        }

        return redirect()->route('admin.designation.index');
    }

    /**
     * Toggle active/inactive status of the designation.
     */
    public function toggleStatus(Designation $designation): RedirectResponse
    {
        $designation->update(['status' => ! $designation->status]);

        $statusText = $designation->status ? 'সক্রিয়' : 'নিষ্ক্রিয়';

        if (function_exists('toastr')) {
            toastr()->success("পদবী '{$designation->name}' এখন {$statusText} করা হয়েছে!", 'স্ট্যাটাস আপডেট');
        }

        return back();
    }

    /**
     * Remove the specified designation from storage.
     */
    public function destroy(Designation $designation): RedirectResponse
    {
        if ($designation->employees()->exists()) {
            if (function_exists('toastr')) {
                toastr()->error("এই পদবীতে কর্মচারী নিয়োজিত থাকায় এটি মুছে ফেলা সম্ভব নয়! পদবী পরিবর্তন করুন বা নিষ্ক্রিয় করুন।", 'মুছে ফেলা যাবে না');
            }

            return back();
        }

        $name = $designation->name;
        $designation->delete();

        if (function_exists('toastr')) {
            toastr()->success("পদবী '{$name}' সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.designation.index');
    }
}
