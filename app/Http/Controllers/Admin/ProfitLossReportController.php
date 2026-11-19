<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\SalePayment;
use App\Models\Sales;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfitLossReportController extends Controller
{
    /**
     * Display the Profit & Loss and Income vs Expense Report.
     */
    public function index(Request $request): View
    {
        $datePreset = $request->query('date_preset', 'this_month');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // Resolve date preset boundaries
        if ($datePreset !== 'custom' && ! empty($datePreset)) {
            switch ($datePreset) {
                case 'today':
                    $fromDate = Carbon::today()->toDateString();
                    $toDate = Carbon::today()->toDateString();
                    break;
                case 'this_week':
                    $fromDate = Carbon::now()->startOfWeek()->toDateString();
                    $toDate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'this_month':
                    $fromDate = Carbon::now()->startOfMonth()->toDateString();
                    $toDate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'last_month':
                    $fromDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                    $toDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'this_year':
                    $fromDate = Carbon::now()->startOfYear()->toDateString();
                    $toDate = Carbon::now()->endOfYear()->toDateString();
                    break;
                case 'all':
                    $fromDate = null;
                    $toDate = null;
                    break;
                default:
                    $fromDate = Carbon::now()->startOfMonth()->toDateString();
                    $toDate = Carbon::now()->endOfMonth()->toDateString();
                    break;
            }
        } elseif ($datePreset === 'custom') {
            if (empty($fromDate)) {
                $fromDate = Carbon::now()->startOfMonth()->toDateString();
            }
            if (empty($toDate)) {
                $toDate = Carbon::now()->toDateString();
            }
        }

        // 1. REVENUE / INCOME CALCULATIONS
        $salesQuery = Sales::query();
        $salePaymentsQuery = SalePayment::query();

        if (! empty($fromDate) && ! empty($toDate)) {
            $salesQuery->whereBetween('sale_date', [$fromDate, $toDate]);
            $salePaymentsQuery->whereBetween('payment_date', [$fromDate, $toDate]);
        } elseif (! empty($fromDate)) {
            $salesQuery->where('sale_date', '>=', $fromDate);
            $salePaymentsQuery->where('payment_date', '>=', $fromDate);
        } elseif (! empty($toDate)) {
            $salesQuery->where('sale_date', '<=', $toDate);
            $salePaymentsQuery->where('payment_date', '<=', $toDate);
        }

        $totalSales = (float) $salesQuery->sum('total_amount');
        $totalSalesInvoices = (int) $salesQuery->count();
        $totalSalesCashReceived = (float) $salePaymentsQuery->sum('amount');
        $totalSalesDue = (float) $salesQuery->sum('due_amount');

        // 2. EXPENDITURES & COSTS CALCULATIONS
        // Purchases (Hatchery / Fish Fry)
        $purchaseQuery = Purchase::query();
        $purchasePaymentQuery = PurchasePayment::query();

        if (! empty($fromDate) && ! empty($toDate)) {
            $purchaseQuery->whereBetween('purchase_date', [$fromDate, $toDate]);
            $purchasePaymentQuery->whereBetween('payment_date', [$fromDate, $toDate]);
        } elseif (! empty($fromDate)) {
            $purchaseQuery->where('purchase_date', '>=', $fromDate);
            $purchasePaymentQuery->where('payment_date', '>=', $fromDate);
        } elseif (! empty($toDate)) {
            $purchaseQuery->where('purchase_date', '<=', $toDate);
            $purchasePaymentQuery->where('payment_date', '<=', $toDate);
        }

        $totalPurchases = (float) $purchaseQuery->sum('total_amount');
        $totalPurchasesPaid = (float) $purchasePaymentQuery->sum('amount');
        $totalPurchasesDue = (float) $purchaseQuery->sum('due_amount');
        $totalPurchaseInvoices = (int) $purchaseQuery->count();

        // Staff Salaries
        $salaryQuery = EmployeeSalary::query();
        if (! empty($fromDate) && ! empty($toDate)) {
            $salaryQuery->whereBetween('payment_date', [$fromDate, $toDate]);
        } elseif (! empty($fromDate)) {
            $salaryQuery->where('payment_date', '>=', $fromDate);
        } elseif (! empty($toDate)) {
            $salaryQuery->where('payment_date', '<=', $toDate);
        }
        $totalSalariesPaid = (float) $salaryQuery->sum('net_salary');
        $totalSalaryCount = (int) $salaryQuery->count();

        // Staff Advances
        $advanceQuery = EmployeeAdvance::query();
        if (! empty($fromDate) && ! empty($toDate)) {
            $advanceQuery->whereBetween('date', [$fromDate, $toDate]);
        } elseif (! empty($fromDate)) {
            $advanceQuery->where('date', '>=', $fromDate);
        } elseif (! empty($toDate)) {
            $advanceQuery->where('date', '<=', $toDate);
        }
        $totalAdvancesGiven = (float) $advanceQuery->sum('amount');

        // Daily / Operational Expenses
        $expenseQuery = Expense::query();
        if (! empty($fromDate) && ! empty($toDate)) {
            $expenseQuery->whereBetween('date', [$fromDate, $toDate]);
        } elseif (! empty($fromDate)) {
            $expenseQuery->where('date', '>=', $fromDate);
        } elseif (! empty($toDate)) {
            $expenseQuery->where('date', '<=', $toDate);
        }
        $totalDailyExpenses = (float) $expenseQuery->sum('amount');
        $totalExpenseCount = (int) $expenseQuery->count();

        // Category-wise Breakdown of General Expenses
        $expenseCategoryBreakdown = Expense::query()
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->when(! empty($fromDate) && ! empty($toDate), function ($q) use ($fromDate, $toDate): void {
                $q->whereBetween('expenses.date', [$fromDate, $toDate]);
            })
            ->select(
                'expense_categories.name',
                DB::raw('SUM(expenses.amount) as total_amount'),
                DB::raw('COUNT(expenses.id) as count')
            )
            ->groupBy('expense_categories.name')
            ->orderByDesc('total_amount')
            ->get();

        // 3. AGGREGATES & NET PROFIT / LOSS
        // Total Cost = Purchases + Salaries + Daily Expenses
        $totalExpensesAndCosts = $totalPurchases + $totalSalariesPaid + $totalDailyExpenses;

        // Accrual / Business Net Profit = Total Sales - Total Expenses & Costs
        $netProfit = $totalSales - $totalExpensesAndCosts;
        $isProfit = $netProfit >= 0;
        $profitMargin = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) : 0;

        // Cash Flow Balance = Cash Inflow - Cash Outflow
        $cashInflow = $totalSalesCashReceived;
        $cashOutflow = $totalPurchasesPaid + $totalSalariesPaid + $totalDailyExpenses;
        $netCashFlow = $cashInflow - $cashOutflow;

        // 4. YEARLY MONTH-BY-MONTH TREND (Current Year)
        $currentYear = Carbon::now()->year;
        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $startOfMonth = Carbon::create($currentYear, $m, 1)->startOfMonth()->toDateString();
            $endOfMonth = Carbon::create($currentYear, $m, 1)->endOfMonth()->toDateString();

            $monthSales = (float) Sales::whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
            $monthPurchases = (float) Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
            $monthSalaries = (float) EmployeeSalary::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('net_salary');
            $monthExpenses = (float) Expense::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
            $monthCost = $monthPurchases + $monthSalaries + $monthExpenses;
            $monthNet = $monthSales - $monthCost;

            $monthlyTrend[] = [
                'month_num' => $m,
                'month_name' => Carbon::create($currentYear, $m, 1)->translatedFormat('F'),
                'sales' => $monthSales,
                'purchases' => $monthPurchases,
                'salaries' => $monthSalaries,
                'expenses' => $monthExpenses,
                'total_cost' => $monthCost,
                'net_profit' => $monthNet,
            ];
        }

        $settings = Setting::first();

        return view('admin.pages.reports.profit-loss', compact(
            'datePreset',
            'fromDate',
            'toDate',
            'totalSales',
            'totalSalesInvoices',
            'totalSalesCashReceived',
            'totalSalesDue',
            'totalPurchases',
            'totalPurchasesPaid',
            'totalPurchasesDue',
            'totalPurchaseInvoices',
            'totalSalariesPaid',
            'totalSalaryCount',
            'totalAdvancesGiven',
            'totalDailyExpenses',
            'totalExpenseCount',
            'expenseCategoryBreakdown',
            'totalExpensesAndCosts',
            'netProfit',
            'isProfit',
            'profitMargin',
            'cashInflow',
            'cashOutflow',
            'netCashFlow',
            'monthlyTrend',
            'settings'
        ));
    }
}
