<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
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

        $query = Supplier::query()->latest('id');

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
        return view('admin.pages.supplier.create');
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
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'সরবরাহকারীর নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
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
        return view('admin.pages.supplier.edit', compact('supplier'));
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
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'সরবরাহকারীর নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $supplier->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('সরবরাহকারীর তথ্য সফলভাবে আপডেট করা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.supplier.index');
    }

    /**
     * Toggle the status of the specified supplier.
     */
    public function toggleStatus(Supplier $supplier): RedirectResponse
    {
        $supplier->update([
            'status' => ! $supplier->status,
        ]);

        $statusText = $supplier->status ? 'সক্রিয়' : 'নিষ্ক্রিয়';

        if (function_exists('toastr')) {
            toastr()->success("সরবরাহকারীকে {$statusText} করা হয়েছে!", 'স্ট্যাটাস আপডেট');
        }

        return back();
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
}
