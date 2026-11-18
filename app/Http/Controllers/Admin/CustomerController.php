<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
