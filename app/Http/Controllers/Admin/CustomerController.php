<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with their balance stats.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $divisionId = $request->query('division_id');
        $districtId = $request->query('district_id');
        $zoneId = $request->query('zone_id');

        // Sanitize hierarchy: ensure selected district belongs to division and zone belongs to district
        if (! empty($divisionId) && ! empty($districtId)) {
            $districtBelongs = District::where('id', $districtId)->where('division_id', $divisionId)->exists();
            if (! $districtBelongs) {
                $districtId = null;
                $zoneId = null;
            }
        }

        if (! empty($districtId) && ! empty($zoneId)) {
            $zoneBelongs = Zone::where('id', $zoneId)->where('district_id', $districtId)->exists();
            if (! $zoneBelongs) {
                $zoneId = null;
            }
        }

        $query = Customer::query()
            ->with(['division', 'district', 'zone'])
            ->withSum('sales as total_sales', 'total_amount')
            ->withSum('sales as total_paid', 'paid_amount')
            ->withSum('sales as total_due', 'due_amount')
            ->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($divisionId)) {
            $query->where('division_id', $divisionId);
        }

        if (! empty($districtId)) {
            $query->where('district_id', $districtId);
        }

        if (! empty($zoneId)) {
            $query->where('zone_id', $zoneId);
        }

        $customers = $query->paginate(15)->withQueryString();

        $stats = [
            'total_customers' => Customer::count(),
            'total_due_customers' => Customer::whereHas('sales', function ($q): void {
                $q->where('due_amount', '>', 0);
            })->count(),
            'total_sales' => (float) Customer::withSum('sales as total_sales', 'total_amount')->get()->sum('total_sales'),
            'total_due' => (float) Customer::withSum('sales as total_due', 'due_amount')->get()->sum('total_due'),
        ];

        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.customer.index', compact(
            'customers',
            'stats',
            'divisions',
            'districts',
            'zones',
            'search',
            'divisionId',
            'districtId',
            'zoneId'
        ));
    }

    /**
     * Show the form for creating a new customer before any sale.
     */
    public function create(): View
    {
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.customer.create', compact('divisions', 'districts', 'zones'));
    }

    /**
     * Store a newly created customer in storage (without requiring a sale).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:30', 'unique:customers,mobile'],
            'email' => ['nullable', 'email', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'কাস্টমারের নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'mobile.unique' => 'এই মোবাইল নম্বরের কাস্টমার ইতিমধ্যে সিস্টেমে সংরক্ষিত আছে।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
            'division_id.exists' => 'নির্বাচিত বিভাগ সঠিক নয়।',
            'district_id.exists' => 'নির্বাচিত জেলা সঠিক নয়।',
            'zone_id.exists' => 'নির্বাচিত জোন সঠিক নয়।',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;

        $customer = Customer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন কাস্টমার সফলভাবে সংরক্ষিত হয়েছে!',
                'customer' => $customer->load(['division', 'district', 'zone']),
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success("নতুন কাস্টমার '{$customer->name}' সফলভাবে যোগ করা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.customer.index');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.customer.edit', compact('customer', 'divisions', 'districts', 'zones'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:30', Rule::unique('customers', 'mobile')->ignore($customer->id)],
            'email' => ['nullable', 'email', 'max:100'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable'],
        ], [
            'name.required' => 'কাস্টমারের নাম আবশ্যক।',
            'mobile.required' => 'মোবাইল নম্বর আবশ্যক।',
            'mobile.unique' => 'এই মোবাইল নম্বরের অন্য একজন কাস্টমার ইতিমধ্যে সংরক্ষিত আছে।',
            'email.email' => 'একটি সঠিক ইমেইল ঠিকানা প্রদান করুন।',
            'division_id.exists' => 'নির্বাচিত বিভাগ সঠিক নয়।',
            'district_id.exists' => 'নির্বাচিত জেলা সঠিক নয়।',
            'zone_id.exists' => 'নির্বাচিত জোন সঠিক নয়।',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : false;

        $customer->update($validated);

        if (function_exists('toastr')) {
            toastr()->success("কাস্টমার '{$customer->name}'-এর তথ্য সফলভাবে হালনাগাদ করা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.customer.index');
    }

    /**
     * Toggle the status of the specified customer.
     */
    public function toggleStatus(Customer $customer): JsonResponse|RedirectResponse
    {
        $customer->update(['status' => ! $customer->status]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $customer->status,
                'message' => 'কাস্টমারের স্ট্যাটাস পরিবর্তিত হয়েছে।',
            ]);
        }

        if (function_exists('toastr')) {
            toastr()->success('কাস্টমারের সক্রিয়/নিষ্ক্রিয় স্ট্যাটাস পরিবর্তিত হয়েছে!', 'সফল');
        }

        return back();
    }

    /**
     * Display the specified customer ledger profile and statements.
     */
    public function show(Customer $customer): View
    {
        $customer->load(['division', 'district', 'zone']);

        $sales = $customer->sales()
            ->with(['items.product', 'payments'])
            ->latest('sale_date')
            ->latest('id')
            ->get();

        $stats = [
            'total_invoices' => $sales->count(),
            'total_purchased' => (float) $sales->sum('total_amount'),
            'total_paid' => (float) $sales->sum('paid_amount'),
            'total_due' => (float) $sales->sum('due_amount'),
        ];

        return view('admin.pages.customer.show', compact('customer', 'sales', 'stats'));
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $name = $customer->name;
        $customer->delete();

        if (function_exists('toastr')) {
            toastr()->success("কাস্টমার {$name} সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.customer.index');
    }
}
