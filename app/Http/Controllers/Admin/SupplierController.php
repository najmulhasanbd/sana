<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Supplier;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Supplier::query()->with(['division', 'district', 'zone'])->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $suppliers = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('status', true)->count(),
            'inactive' => Supplier::where('status', false)->count(),
        ];

        return view('admin.pages.supplier.index', compact('suppliers', 'stats', 'search', 'status'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(): View
    {
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.supplier.create', compact('divisions', 'districts', 'zones'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'সরবরাহকারীর নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
            'division_id.exists' => 'নির্বাচিত বিভাগ সঠিক নয়।',
            'district_id.exists' => 'নির্বাচিত জেলা সঠিক নয়।',
            'zone_id.exists' => 'নির্বাচিত জোন সঠিক নয়।',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;

        Supplier::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('নতুন হ্যাচারি সরবরাহকারী সফলভাবে যোগ করা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.supplier.index');
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier): View
    {
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.supplier.edit', compact('supplier', 'divisions', 'districts', 'zones'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'সরবরাহকারীর নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
            'division_id.exists' => 'নির্বাচিত বিভাগ সঠিক নয়।',
            'district_id.exists' => 'নির্বাচিত জেলা সঠিক নয়।',
            'zone_id.exists' => 'নির্বাচিত জোন সঠিক নয়।',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $supplier->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('সরবরাহকারীর তথ্য সফলভাবে আপডেট করা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.supplier.index');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        if (function_exists('toastr')) {
            toastr()->success('সরবরাহকারী সফলভাবে মুছে ফেলা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.supplier.index');
    }

    /**
     * Toggle the status of the specified supplier via AJAX or standard request.
     */
    public function toggleStatus(Supplier $supplier): JsonResponse|RedirectResponse
    {
        $supplier->status = ! $supplier->status;
        $supplier->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $supplier->status,
                'message' => $supplier->status ? 'সরবরাহকারী সক্রিয় করা হয়েছে' : 'সরবরাহকারী নিষ্ক্রিয় করা হয়েছে',
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success('স্ট্যাটাস সফলভাবে পরিবর্তন করা হয়েছে!', 'সফল');
        }

        return back();
    }
}
