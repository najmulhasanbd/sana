<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Sales;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with real-time business statistics.
     */
    public function dashboard(): View
    {
        // 1. Core Financial Aggregates
        $totalCustomerDue = (float) Sales::sum('due_amount');
        $customersWithDueCount = Customer::whereHas('sales', function ($q): void {
            $q->where('due_amount', '>', 0);
        })->count();

        $totalSales = (float) Sales::sum('total_amount');
        $totalSalesCount = (int) Sales::count();
        $totalCollected = (float) Sales::sum('paid_amount');

        $totalPurchases = (float) Purchase::sum('total_amount');
        $totalSupplierDue = (float) Purchase::sum('due_amount');
        $totalPurchasesCount = (int) Purchase::count();

        $totalDailyExpenses = (float) Expense::sum('amount');
        $totalSalariesPaid = (float) EmployeeSalary::sum('net_salary');

        // Total Cost = Purchases + Salaries + Daily Expenses
        $totalOverallCosts = $totalPurchases + $totalSalariesPaid + $totalDailyExpenses;
        $netProfit = $totalSales - $totalOverallCosts;
        $isNetProfitPositive = $netProfit >= 0;

        // 2. Today's Quick Pulse
        $today = Carbon::today()->toDateString();
        $todaySales = (float) Sales::whereDate('sale_date', $today)->sum('total_amount');
        $todayCashCollected = (float) SalePayment::whereDate('payment_date', $today)->sum('amount');
        $todayExpenses = (float) Expense::whereDate('date', $today)->sum('amount');

        // 3. This Month's Metrics
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $monthSales = (float) Sales::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_amount');
        $monthPurchases = (float) Purchase::whereMonth('purchase_date', $currentMonth)
            ->whereYear('purchase_date', $currentYear)
            ->sum('total_amount');
        $monthExpenses = (float) Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
        $monthSalaries = (float) EmployeeSalary::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('net_salary');
        $monthCost = $monthPurchases + $monthExpenses + $monthSalaries;
        $monthNetProfit = $monthSales - $monthCost;

        // 4. Quick Entity Counts
        $totalCustomersCount = (int) Customer::count();
        $totalSuppliersCount = (int) Supplier::count();
        $totalEmployeesCount = (int) Employee::where('status', true)->count();
        $dueInvoicesCount = (int) Sales::where('due_amount', '>', 0)->count();

        // 5. Recent Sales
        $recentSales = Sales::with(['customer'])
            ->latest('id')
            ->take(6)
            ->get();

        // 6. Top Selling Fish Fry / Products
        $topProducts = SaleItem::query()
            ->select('sale_product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->with('product')
            ->groupBy('sale_product_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // 7. Last 6 Months Chart Data
        $chartLabels = [];
        $chartSales = [];
        $chartCosts = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $chartLabels[] = $monthDate->translatedFormat('M Y');

            $mSales = (float) Sales::whereMonth('sale_date', $monthDate->month)
                ->whereYear('sale_date', $monthDate->year)
                ->sum('total_amount');
            $mPurchases = (float) Purchase::whereMonth('purchase_date', $monthDate->month)
                ->whereYear('purchase_date', $monthDate->year)
                ->sum('total_amount');
            $mExp = (float) Expense::whereMonth('date', $monthDate->month)
                ->whereYear('date', $monthDate->year)
                ->sum('amount');
            $mSal = (float) EmployeeSalary::whereMonth('payment_date', $monthDate->month)
                ->whereYear('payment_date', $monthDate->year)
                ->sum('net_salary');

            $chartSales[] = $mSales;
            $chartCosts[] = $mPurchases + $mExp + $mSal;
        }

        return view('admin.index', compact(
            'totalCustomerDue',
            'customersWithDueCount',
            'totalSales',
            'totalSalesCount',
            'totalCollected',
            'totalPurchases',
            'totalSupplierDue',
            'totalPurchasesCount',
            'totalDailyExpenses',
            'totalSalariesPaid',
            'totalOverallCosts',
            'netProfit',
            'isNetProfitPositive',
            'todaySales',
            'todayCashCollected',
            'todayExpenses',
            'monthSales',
            'monthPurchases',
            'monthExpenses',
            'monthSalaries',
            'monthCost',
            'monthNetProfit',
            'totalCustomersCount',
            'totalSuppliersCount',
            'totalEmployeesCount',
            'dueInvoicesCount',
            'recentSales',
            'topProducts',
            'chartLabels',
            'chartSales',
            'chartCosts'
        ));
    }

    /**
     * Clear application cache using Artisan commands.
     */
    public function clearCache(): RedirectResponse
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        if (function_exists('toastr')) {
            toastr()->success('সিস্টেম ক্যাশ (Views, Routes, Config & Cache) সফলভাবে পরিষ্কার করা হয়েছে!', 'ক্যাশ ক্লিয়ার');
        }

        return back();
    }
}
