<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\SaleProduct;
use App\Models\Sales;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $paymentStatus = $request->query('payment_status');
        $customerId = $request->query('customer_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = Sales::query()
            ->with(['customer.district', 'customer.division', 'items.product', 'payments'])
            ->latest('sale_date')
            ->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($paymentStatus)) {
            $query->where('payment_status', $paymentStatus);
        }

        if (! empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        if (! empty($fromDate)) {
            $query->whereDate('sale_date', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $query->whereDate('sale_date', '<=', $toDate);
        }

        $sales = $query->paginate(15)->withQueryString();

        $stats = [
            'total_count' => Sales::count(),
            'total_amount' => (float) Sales::sum('total_amount'),
            'total_paid' => (float) Sales::sum('paid_amount'),
            'total_due' => (float) Sales::sum('due_amount'),
        ];

        $customers = Customer::active()->orderBy('name')->get();

        return view('admin.pages.sales.index', compact(
            'sales',
            'stats',
            'customers',
            'search',
            'paymentStatus',
            'customerId',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create(): View
    {
        $customers = Customer::active()
            ->with(['district', 'division', 'zone'])
            ->withSum('sales as total_due', 'due_amount')
            ->orderBy('name')
            ->get();
        $products = SaleProduct::orderBy('name')->get();
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::with('division')->active()->orderBy('name')->get();
        $zones = Zone::with('district')->active()->orderBy('name')->get();
        $nextInvoiceNo = Sales::generateInvoiceNo();

        return view('admin.pages.sales.create', compact(
            'customers',
            'products',
            'divisions',
            'districts',
            'zones',
            'nextInvoiceNo'
        ));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sale_date' => ['required', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_mobile' => ['required', 'string', 'max:30'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_product_id' => ['required', 'distinct', 'exists:sale_products,id'],
            'items.*.box' => ['nullable', 'string', 'max:50'],
            'items.*.poly' => ['nullable', 'string', 'max:50'],
            'items.*.meer' => ['nullable', 'string', 'max:50'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'sale_date.required' => 'বিক্রয়ের তারিখ আবশ্যক।',
            'customer_name.required' => 'কাস্টমারের নাম আবশ্যক।',
            'customer_mobile.required' => 'কাস্টমারের মোবাইল নম্বর আবশ্যক।',
            'items.required' => 'অন্তত একটি পণ্য যোগ করুন।',
            'items.min' => 'অন্তত একটি পণ্য যোগ করুন।',
            'items.*.sale_product_id.required' => 'পণ্য নির্বাচন করুন।',
            'items.*.sale_product_id.distinct' => 'একই পণ্য একাধিকবার নির্বাচন করা যাবে না।',
            'items.*.quantity.required' => 'পরিমাণ আবশ্যক।',
            'items.*.quantity.gt' => 'পরিমাণ ০ থেকে বেশি হতে হবে।',
            'items.*.unit.required' => 'একক (Unit) নির্বাচন করুন।',
            'items.*.unit_price.required' => 'একক দর প্রদান করুন।',
        ]);

        $sale = DB::transaction(function () use ($validated): Sales {
            // Find or Create Customer with unique mobile
            $customer = Customer::updateOrCreate(
                ['mobile' => $validated['customer_mobile']],
                [
                    'name' => $validated['customer_name'],
                    'division_id' => $validated['division_id'] ?? null,
                    'district_id' => $validated['district_id'] ?? null,
                    'zone_id' => $validated['zone_id'] ?? null,
                    'address' => $validated['customer_address'] ?? null,
                    'status' => true,
                ]
            );

            // Calculate total line amounts
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $lineTotal = round($qty * $price, 2);
                $totalAmount += $lineTotal;

                $itemsData[] = [
                    'sale_product_id' => $item['sale_product_id'],
                    'box' => $item['box'] ?? null,
                    'poly' => $item['poly'] ?? null,
                    'meer' => $item['meer'] ?? null,
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? 'পিস',
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

            $invoiceNo = Sales::generateInvoiceNo();

            $sale = Sales::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $customer->id,
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'note' => $validated['note'] ?? null,
            ]);

            $sale->items()->createMany($itemsData);

            if ($paidAmount > 0) {
                $sale->payments()->create([
                    'payment_date' => $validated['sale_date'],
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'note' => 'বিক্রয়কালীন প্রাথমিক পরিশোধ',
                ]);
            }

            return $sale;
        });

        if (function_exists('toastr')) {
            toastr()->success("বিক্রয় সফলভাবে সংরক্ষিত হয়েছে! চালান নম্বর: {$sale->invoice_no}", 'সফল');
        }

        return redirect()->route('admin.sales.show', $sale);
    }

    /**
     * Display the specified sale invoice with items and payment history.
     */
    public function show(Sales $sale): View
    {
        $sale->load([
            'customer.district',
            'customer.division',
            'customer.zone',
            'items.product',
            'payments',
        ]);

        // Customer's previous due (before this invoice)
        $previousDue = 0;
        if ($sale->customer_id) {
            $previousDue = (float) Sales::where('customer_id', $sale->customer_id)
                ->where('id', '<', $sale->id)
                ->sum('due_amount');
        }

        $grandTotal = $sale->total_amount + $previousDue;
        $totalOutstanding = $previousDue + $sale->due_amount;

        return view('admin.pages.sales.show', compact('sale', 'previousDue', 'grandTotal', 'totalOutstanding'));
    }

    /**
     * Store a due payment for this sale.
     */
    public function addPayment(Request $request, Sales $sale): RedirectResponse
    {
        $maxAllowed = (float) $sale->due_amount;

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

        DB::transaction(function () use ($sale, $validated): void {
            $sale->payments()->create($validated);
            $sale->recalculatePayments();
        });

        if (function_exists('toastr')) {
            toastr()->success('বকেয়া টাকা সফলভাবে পরিশোধ করা হয়েছে!', 'সফল');
        }

        return back();
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy(Sales $sale): RedirectResponse
    {
        $invoiceNo = $sale->invoice_no;
        $sale->delete();

        if (function_exists('toastr')) {
            toastr()->success("বিক্রয় চালান {$invoiceNo} সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.sales.index');
    }
}
