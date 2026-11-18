<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleProductController extends Controller
{
    /**
     * Display a listing of sale products (Single page with modals for Create & Edit).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = SaleProduct::query()->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        $products = $query->paginate(15)->withQueryString();
        $totalCount = SaleProduct::count();

        return view('admin.pages.sale-product.index', compact('products', 'search', 'totalCount'));
    }

    /**
     * Store a newly created sale product in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:sale_products,name'],
        ], [
            'name.required' => 'পণ্যের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের পণ্য ইতিমধ্যে যোগ করা আছে।',
        ]);

        $product = SaleProduct::create($validated);

        if (function_exists('toastr')) {
            toastr()->success('নতুন বিক্রয় পণ্য সফলভাবে যোগ করা হয়েছে!', 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'নতুন বিক্রয় পণ্য সফলভাবে যোগ করা হয়েছে!',
                'data' => $product,
            ]);
        }

        return redirect()->route('admin.sale-product.index');
    }

    /**
     * Update the specified sale product in storage.
     */
    public function update(Request $request, SaleProduct $saleProduct): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:sale_products,name,'.$saleProduct->id],
        ], [
            'name.required' => 'পণ্যের নাম প্রদান করুন।',
            'name.unique' => 'এই নামের পণ্য ইতিমধ্যে যোগ করা আছে।',
        ]);

        $saleProduct->update($validated);

        if (function_exists('toastr')) {
            toastr()->success('পণ্যের নাম সফলভাবে আপডেট করা হয়েছে!', 'সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'পণ্যের নাম সফলভাবে আপডেট করা হয়েছে!',
                'data' => $saleProduct,
            ]);
        }

        return redirect()->route('admin.sale-product.index');
    }

    /**
     * Remove the specified sale product from storage.
     */
    public function destroy(SaleProduct $saleProduct): RedirectResponse
    {
        $saleProduct->delete();

        if (function_exists('toastr')) {
            toastr()->success('পণ্য সফলভাবে মুছে ফেলা হয়েছে!', 'সফল');
        }

        return redirect()->route('admin.sale-product.index');
    }
}
