<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseProductController extends Controller
{
    /**
     * Display a listing of purchase products (Single page with modals for Create & Edit).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = PurchaseProduct::query()->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        $products = $query->paginate(15)->withQueryString();
        $totalCount = PurchaseProduct::count();

        return view('admin.pages.purchase-product.index', compact('products', 'search', 'totalCount'));
    }

    /**
     * Store a newly created purchase product in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:purchase_products,name'],
        ], [
            'name.required' => 'পণ্যের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের পণ্য ইতিমধ্যে যোগ করা আছে।',
        ]);

        $product = PurchaseProduct::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('নতুন ক্রয় পণ্য সফলভাবে যোগ করা হয়েছে!', 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন ক্রয় পণ্য সফলভাবে যোগ করা হয়েছে!',
                'data' => $product,
            ]);
        }

        return redirect()->route('admin.purchase-product.index');
    }

    /**
     * Update the specified purchase product in storage.
     */
    public function update(Request $request, PurchaseProduct $purchaseProduct): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:purchase_products,name,'.$purchaseProduct->id],
        ], [
            'name.required' => 'পণ্যের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের পণ্য ইতিমধ্যে যোগ করা আছে।',
        ]);

        $purchaseProduct->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('পণ্যের নাম সফলভাবে আপডেট করা হয়েছে!', 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'পণ্যের নাম সফলভাবে আপডেট করা হয়েছে!',
                'data' => $purchaseProduct,
            ]);
        }

        return redirect()->route('admin.purchase-product.index');
    }

    /**
     * Remove the specified purchase product from storage.
     */
    public function destroy(PurchaseProduct $purchaseProduct): RedirectResponse
    {
        $purchaseProduct->delete();

        if (function_exists('toastr')) {
            toastr()->success('পণ্য সফলভাবে মুছে ফেলা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.purchase-product.index');
    }
}
