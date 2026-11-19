<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Display a listing of daily expenses with comprehensive filters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $paymentMethod = $request->query('payment_method');
        $datePreset = $request->query('date_preset', 'all');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // Apply date preset if selected and custom dates not explicitly overridden
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
        }

        $query = Expense::query()->with('category');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($categoryId)) {
            $query->where('expense_category_id', $categoryId);
        }

        if (! empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        if (! empty($fromDate) || ! empty($toDate)) {
            $query->dateBetween($fromDate, $toDate);
        }

        // Clone query for calculating filtered sum
        $filteredTotalAmount = (float) (clone $query)->sum('amount');
        $filteredCount = (int) (clone $query)->count();

        $expenses = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        // High level overview metrics
        $todayAmount = (float) Expense::whereDate('date', Carbon::today())->sum('amount');
        $thisMonthAmount = (float) Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');
        $totalAllTimeAmount = (float) Expense::sum('amount');

        $categories = ExpenseCategory::active()->orderBy('name')->get();

        return view('admin.pages.expense.index', compact(
            'expenses',
            'categories',
            'search',
            'categoryId',
            'paymentMethod',
            'datePreset',
            'fromDate',
            'toDate',
            'filteredTotalAmount',
            'filteredCount',
            'todayAmount',
            'thisMonthAmount',
            'totalAllTimeAmount'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'expense_category_id.required' => 'খরচের খাত নির্বাচন করুন।',
            'expense_category_id.exists' => 'নির্বাচিত খরচের খাতটি সঠিক নয়।',
            'date.required' => 'খরচের তারিখ নির্বাচন করুন।',
            'amount.required' => 'খরচের টাকার পরিমাণ লিখুন।',
            'amount.min' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম নির্বাচন করুন।',
        ]);

        $expense = Expense::create($validated);

        if (function_exists('toastr')) {
            $formattedAmount = number_format((float) $expense->amount, 2);
            toastr()->success("৳ {$formattedAmount} টাকার খরচ সফলভাবে যুক্ত করা হয়েছে!", 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন খরচ সফলভাবে সংরক্ষণ করা হয়েছে!',
                'data' => $expense->load('category'),
            ]);
        }

        return redirect()->route('admin.expense.index');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'expense_category_id.required' => 'খরচের খাত নির্বাচন করুন।',
            'expense_category_id.exists' => 'নির্বাচিত খরচের খাতটি সঠিক নয়।',
            'date.required' => 'খরচের তারিখ নির্বাচন করুন।',
            'amount.required' => 'খরচের টাকার পরিমাণ লিখুন।',
            'amount.min' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'payment_method.required' => 'পেমেন্ট মাধ্যম নির্বাচন করুন।',
        ]);

        $expense->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('খরচের তথ্য সফলভাবে আপডেট করা হয়েছে!', 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'খরচের তথ্য সফলভাবে আপডেট করা হয়েছে!',
                'data' => $expense->load('category'),
            ]);
        }

        return redirect()->route('admin.expense.index');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        if (function_exists('toastr')) {
            toastr()->success('খরচের এন্ট্রি সফলভাবে মুছে ফেলা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.expense.index');
    }
}
