<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDueController extends Controller
{
    /**
     * Display the Customer Due Sheet with Area Grouping & Filters.
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

        $isFiltered = $request->has('filter_applied') || $request->hasAny(['search', 'division_id', 'district_id', 'zone_id']);

        $groupByArea = $request->has('group_by_area')
            ? $request->boolean('group_by_area')
            : ($isFiltered ? false : true);

        $dueOnly = $request->has('due_only')
            ? $request->boolean('due_only')
            : ($isFiltered ? false : true);

        $query = Customer::query()
            ->with(['division', 'district', 'zone'])
            ->withSum('sales as total_sales', 'total_amount')
            ->withSum('sales as total_paid', 'paid_amount')
            ->withSum('sales as total_due', 'due_amount');

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

        if ($dueOnly) {
            $query->whereHas('sales', function ($q): void {
                $q->where('due_amount', '>', 0);
            });
        }

        $customers = $query->get()->filter(function ($cust) use ($dueOnly): bool {
            return ! $dueOnly || (float) ($cust->total_due ?? 0) > 0;
        })->values();

        // Summary statistics
        $stats = [
            'total_customers' => $customers->count(),
            'total_sales' => (float) $customers->sum('total_sales'),
            'total_paid' => (float) $customers->sum('total_paid'),
            'total_due' => (float) $customers->sum('total_due'),
        ];

        // Grouping by area if enabled
        $groupedCustomers = null;
        if ($groupByArea) {
            $groupedCustomers = $customers->groupBy(function ($cust): string {
                $area = collect([$cust->zone?->name, $cust->district?->name, $cust->division?->name])
                    ->filter()
                    ->implode(', ');

                return $area ?: 'অন্যান্য এলাকা (অনির্দিষ্ট)';
            });
        }

        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();

        return view('admin.pages.reports.customer-due', compact(
            'customers',
            'groupedCustomers',
            'stats',
            'divisions',
            'districts',
            'zones',
            'search',
            'divisionId',
            'districtId',
            'zoneId',
            'groupByArea',
            'dueOnly'
        ));
    }
}
