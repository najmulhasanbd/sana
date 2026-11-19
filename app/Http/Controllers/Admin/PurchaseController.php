<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\PurchaseProduct;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $paymentStatus = $request->query('payment_status');
        $supplierId = $request->query('supplier_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = Purchase::query()->with(['supplier', 'items.product', 'payments'])->latest('purchase_date')->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($paymentStatus)) {
            $query->where('payment_status', $paymentStatus);
        }

        if (! empty($supplierId)) {
            $query->where('supplier_id', $supplierId);
        }

        if (! empty($fromDate)) {
            $query->whereDate('purchase_date', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $query->whereDate('purchase_date', '<=', $toDate);
        }

        $purchases = $query->paginate(15)->withQueryString();

        $stats = [
            'total_count' => Purchase::count(),
            'total_amount' => (float) Purchase::sum('total_amount'),
            'total_paid' => (float) Purchase::sum('paid_amount'),
            'total_due' => (float) Purchase::sum('due_amount'),
        ];

        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.pages.purchase.index', compact('purchases', 'stats', 'suppliers', 'search', 'paymentStatus', 'supplierId', 'fromDate', 'toDate'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create(): View
    {
        $suppliers = Supplier::active()->with(['district', 'division'])->orderBy('name')->get();
        $products = PurchaseProduct::orderBy('name')->get();
        $nextInvoiceNo = Purchase::generateInvoiceNo();

        return view('admin.pages.purchase.create', compact('suppliers', 'products', 'nextInvoiceNo'));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_product_id' => ['required', 'distinct', 'exists:purchase_products,id'],
            'items.*.box' => ['nullable'],
            'items.*.poly' => ['nullable'],
            'items.*.meer' => ['nullable'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'supplier_id.required' => 'সরবরাহকারী নির্বাচন করুন।',
            'supplier_id.exists' => 'নির্বাচিত সরবরাহকারী পাওয়া যায়নি।',
            'purchase_date.required' => 'ক্রয়ের তারিখ আবশ্যক।',
            'items.required' => 'অন্তত একটি পণ্য যোগ করুন।',
            'items.min' => 'অন্তত একটি পণ্য যোগ করুন।',
            'items.*.purchase_product_id.required' => 'পণ্য নির্বাচন করুন।',
            'items.*.purchase_product_id.distinct' => 'একই পণ্য একাধিকবার নির্বাচন করা যাবে না। প্রতিটি সারিতে ভিন্ন পণ্য নির্বাচন করুন।',
            'items.*.quantity.required' => 'পণ্যের পরিমাণ আবশ্যক।',
            'items.*.quantity.gt' => 'পণ্যের পরিমাণ ০ থেকে বেশি হতে হবে।',
            'items.*.unit_price.required' => 'একক দর প্রদান করুন।',
        ]);

        $purchase = DB::transaction(function () use ($validated, $request): Purchase {
            // Calculate total line amounts
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];

                // In hatchery fry nursery business, fry rate (দর) is per 1,000 fry (প্রতি হাজার পোনার দর).
                // e.g. 21,000 fry @ 370 Tk/thousand = 7,770 Tk.
                // For general goods, normal qty * price applies.
                $isPerThousand = !empty($item['meer']) || in_array($item['unit'] ?? '', ['হাজার', 'পোনা']);
                $lineTotal = $isPerThousand ? round(($qty * $price) / 1000, 2) : round($qty * $price, 2);
                $totalAmount += $lineTotal;

                $itemsData[] = [
                    'purchase_product_id' => $item['purchase_product_id'],
                    'box' => $item['box'] ?? null,
                    'poly' => $item['poly'] ?? null,
                    'meer' => $item['meer'] ?? null,
                    'unit' => $item['unit'] ?? 'পোনা',
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $lineTotal,
                ];
            }

            $paidAmount = isset($validated['paid_amount']) ? (float) $validated['paid_amount'] : 0;
            if ($paidAmount > $totalAmount) {
                $paidAmount = $totalAmount;
            }

            $dueAmount = max(0, $totalAmount - $paidAmount);

            if ($paidAmount >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'due';
            }

            $invoiceNo = Purchase::generateInvoiceNo();

            /** @var Purchase $purchase */
            $purchase = Purchase::create([
                'invoice_no' => $invoiceNo,
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($itemsData as $row) {
                $purchase->items()->create($row);
            }

            // Record initial payment if paid_amount > 0
            if ($paidAmount > 0) {
                $purchase->payments()->create([
                    'payment_date' => $validated['purchase_date'],
                    'amount' => $paidAmount,
                    'payment_method' => $request->input('payment_method') ?: 'Cash',
                    'transaction_id' => $request->input('transaction_id'),
                    'note' => 'প্রাথমিক পরিশোধ (Initial payment)',
                ]);
            }

            return $purchase;
        });

        if (function_exists('toastr')) {
            toastr()->success("ক্রয় সফলভাবে সংরক্ষিত হয়েছে! চালান নম্বর: {$purchase->invoice_no}", 'সফল');
        }

        return redirect()->route('admin.purchase.show', $purchase);
    }

    /**
     * Display the specified purchase invoice with items and payment history.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier.district', 'supplier.division', 'items.product', 'payments']);

        return view('admin.pages.purchase.show', compact('purchase'));
    }

    /**
     * Store a due payment for this purchase.
     */
    public function addPayment(Request $request, Purchase $purchase): RedirectResponse
    {
        $maxAllowed = (float) $purchase->due_amount;

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:'.$maxAllowed],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_date.required' => 'পরিশোধের তারিখ প্রদান করুন।',
            'amount.required' => 'পরিশোধিত টাকার পরিমাণ আবশ্যক।',
            'amount.gt' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'amount.max' => 'টাকার পরিমাণ অবশিষ্ট বকেয়া (৳'.number_format($maxAllowed, 2).') এর বেশি হতে পারবে না।',
            'payment_method.required' => 'পরিশোধের মাধ্যম নির্বাচন করুন।',
        ]);

        DB::transaction(function () use ($purchase, $validated): void {
            $purchase->payments()->create($validated);
            $purchase->recalculatePayments();
        });

        if (function_exists('toastr')) {
            toastr()->success('বকেয়া টাকা সফলভাবে পরিশোধ করা হয়েছে!', 'সফল');
        }

        return back();
    }

    /**
     * Remove the specified purchase from storage.
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        $invoiceNo = $purchase->invoice_no;
        $purchase->delete();

        if (function_exists('toastr')) {
            toastr()->success("চালান {$invoiceNo} সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.purchase.index');
    }
}
