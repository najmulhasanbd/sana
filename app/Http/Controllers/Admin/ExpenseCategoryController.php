<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = ExpenseCategory::query()
            ->withCount('expenses')
            ->withSum('expenses as total_expense_amount', 'amount')
            ->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        $categories = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ExpenseCategory::count(),
            'active' => ExpenseCategory::where('status', true)->count(),
            'inactive' => ExpenseCategory::where('status', false)->count(),
        ];

        return view('admin.pages.expense.category.index', compact('categories', 'stats', 'search'));
    }

    /**
     * Store a newly created expense category.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'খরচের খাতের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের খরচের খাত ইতিমধ্যে যোগ করা আছে।',
        ]);

        $validated['status'] = true;

        $category = ExpenseCategory::create($validated);

        if (function_exists('toastr')) {
            toastr()->success("নতুন খরচের খাত '{$category->name}' সফলভাবে যোগ করা হয়েছে!", 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন খরচের খাত সফলভাবে যোগ করা হয়েছে!',
                'data' => $category,
            ]);
        }

        return redirect()->route('admin.expense-category.index');
    }

    /**
     * Update the specified expense category.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name,' . $expenseCategory->id],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'খরচের খাতের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের খরচের খাত ইতিমধ্যে তালিকায় আছে।',
        ]);

        $expenseCategory->update($validated);

        if (function_exists('toastr')) {
            toastr()->success("খরচের খাত '{$expenseCategory->name}' সফলভাবে আপডেট করা হয়েছে!", 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'খরচের খাত সফলভাবে আপডেট করা হয়েছে!',
                'data' => $expenseCategory,
            ]);
        }

        return redirect()->route('admin.expense-category.index');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update(['status' => ! $expenseCategory->status]);

        $statusLabel = $expenseCategory->status ? 'সক্রিয়' : 'নিষ্ক্রিয়';
        if (function_exists('toastr')) {
            toastr()->info("খরচের খাতের স্ট্যাটাস পরিবর্তিত হয়ে '{$statusLabel}' হয়েছে।", 'স্ট্যাটাস পরিবর্তন');
        }

        return back();
    }

    /**
     * Remove the specified expense category from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->expenses()->count() > 0) {
            if (function_exists('toastr')) {
                toastr()->error("এই খাতের অধীনে খরচ এন্ট্রি রয়েছে, তাই এটি মুছে ফেলা সম্ভব নয়!", 'সতর্কতা');
            }

            return back();
        }

        $expenseCategory->delete();

        if (function_exists('toastr')) {
            toastr()->success('খরচের খাত সফলভাবে মুছে ফেলা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.expense-category.index');
    }
}
