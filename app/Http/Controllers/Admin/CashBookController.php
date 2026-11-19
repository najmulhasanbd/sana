<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashBookController extends Controller
{
    /**
     * Display a listing of cash book transactions (inflow, outflow, balance).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $category = $request->query('category');
        $paymentMethod = $request->query('payment_method');
        $datePreset = $request->query('date_preset', 'this_month');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // Apply date presets
        if ($datePreset !== 'custom' && ! empty($datePreset)) {
            switch ($datePreset) {
                case 'today':
                    $fromDate = Carbon::today()->toDateString();
                    $toDate = Carbon::today()->toDateString();
                    break;
                case 'yesterday':
                    $fromDate = Carbon::yesterday()->toDateString();
                    $toDate = Carbon::yesterday()->toDateString();
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
                default:
                    $fromDate = null;
                    $toDate = null;
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

        // Base query with relationships
        $query = CashBook::query()->with('creator');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($type) && in_array($type, ['deposit', 'expense'])) {
            $query->where('type', $type);
        }

        if (! empty($category)) {
            $query->where('category', $category);
        }

        if (! empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        if (! empty($fromDate) || ! empty($toDate)) {
            $query->dateBetween($fromDate, $toDate);
        }

        // Clone query for totals in current filter scope
        $totalsQuery = clone $query;
        $totalDeposit = (float) (clone $totalsQuery)->where('type', 'deposit')->sum('amount');
        $totalExpense = (float) (clone $totalsQuery)->where('type', 'expense')->sum('amount');
        $netBalance = $totalDeposit - $totalExpense;

        // Overall lifetime summary (unfiltered)
        $lifetimeDeposit = (float) CashBook::where('type', 'deposit')->sum('amount');
        $lifetimeExpense = (float) CashBook::where('type', 'expense')->sum('amount');
        $lifetimeBalance = $lifetimeDeposit - $lifetimeExpense;

        // Paginated list
        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Distinct existing categories for filter dropdown
        $existingCategories = CashBook::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $defaultCategories = CashBook::defaultCategories();

        return view('admin.pages.cash-book.index', compact(
            'transactions',
            'totalDeposit',
            'totalExpense',
            'netBalance',
            'lifetimeDeposit',
            'lifetimeExpense',
            'lifetimeBalance',
            'existingCategories',
            'defaultCategories',
            'search',
            'type',
            'category',
            'paymentMethod',
            'datePreset',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Store a newly created cash book entry in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:deposit,expense'],
            'category' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'type.required' => 'লেনদেনের ধরণ (জমা বা খরচ) নির্বাচন করুন।',
            'category.required' => 'খাত বা উদ্দেশ্যের নাম আবশ্যক।',
            'date.required' => 'লেনদেনের তারিখ আবশ্যক।',
            'amount.required' => 'টাকার পরিমাণ আবশ্যক।',
            'amount.gt' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম আবশ্যক।',
        ]);

        $validated['created_by'] = $request->user()?->id;

        $entry = CashBook::create($validated);

        $typeName = $entry->type === 'deposit' ? 'জমা' : 'খরচ';

        if (function_exists('toastr')) {
            toastr()->success("নতুন {$typeName} সফলভাবে যুক্ত করা হয়েছে! পরিমাণ: ৳".number_format($entry->amount, 2), 'সফল');
        }

        return redirect()->route('admin.cash-book.index');
    }

    /**
     * Update the specified cash book entry in storage.
     */
    public function update(Request $request, CashBook $cashBook): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:deposit,expense'],
            'category' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'type.required' => 'লেনদেনের ধরণ নির্বাচন করুন।',
            'category.required' => 'খাত বা উদ্দেশ্যের নাম আবশ্যক।',
            'date.required' => 'লেনদেনের তারিখ আবশ্যক।',
            'amount.required' => 'টাকার পরিমাণ আবশ্যক।',
            'amount.gt' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম আবশ্যক।',
        ]);

        $cashBook->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('লেনদেনের তথ্য সফলভাবে হালনাগাদ করা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.cash-book.index');
    }

    /**
     * Remove the specified cash book entry from storage.
     */
    public function destroy(CashBook $cashBook): RedirectResponse
    {
        $typeName = $cashBook->type === 'deposit' ? 'জমা' : 'খরচ';
        $amount = number_format($cashBook->amount, 2);

        $cashBook->delete();

        if (function_exists('toastr')) {
            toastr()->success("{$typeName} (৳{$amount}) সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.cash-book.index');
    }
}
