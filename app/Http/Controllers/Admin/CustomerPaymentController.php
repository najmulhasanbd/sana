<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\BanglaNumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function __construct(
        public SmsService $smsService
    ) {}

    /**
     * Display a listing of money receipt vouchers.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $customerId = $request->query('customer_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = CustomerPayment::query()
            ->with(['customer.district', 'customer.division', 'customer.zone', 'creator'])
            ->latest('payment_date')
            ->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($customerId)) {
            $query->where('customer_id', $customerId);
        }

        if (! empty($fromDate)) {
            $query->whereDate('payment_date', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $query->whereDate('payment_date', '<=', $toDate);
        }

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'total_count' => CustomerPayment::count(),
            'total_collected' => (float) CustomerPayment::sum('amount'),
            'today_collected' => (float) CustomerPayment::whereDate('payment_date', now()->toDateString())->sum('amount'),
        ];

        $customers = Customer::active()->orderBy('name')->get();

        return view('admin.pages.customer-payments.index', compact(
            'payments',
            'stats',
            'customers',
            'search',
            'customerId',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Show the form for creating a new money receipt voucher.
     */
    public function create(Request $request): View
    {
        $selectedCustomerId = $request->query('customer_id');

        $customers = Customer::active()
            ->with(['division', 'district', 'zone'])
            ->withSum('sales as total_due', 'due_amount')
            ->orderBy('name')
            ->get();

        $nextVoucherNo = CustomerPayment::generateVoucherNo();

        return view('admin.pages.customer-payments.create', compact('customers', 'selectedCustomerId', 'nextVoucherNo'));
    }

    /**
     * Store a newly collected customer due payment.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
            'send_sms' => ['nullable'],
        ], [
            'customer_id.required' => 'কাস্টমার নির্বাচন করুন।',
            'customer_id.exists' => 'নির্বাচিত কাস্টমার সিস্টেমে পাওয়া যায়নি।',
            'payment_date.required' => 'টাকা জমার তারিখ আবশ্যক।',
            'amount.required' => 'জমার পরিমাণ আবশ্যক।',
            'amount.gt' => 'টাকার পরিমাণ ০ এর বেশি হতে হবে।',
            'payment_method.required' => 'পরিশোধের মাধ্যম নির্বাচন করুন।',
        ]);

        $customerPayment = DB::transaction(function () use ($validated): CustomerPayment {
            $customer = Customer::lockForUpdate()->findOrFail($validated['customer_id']);

            // Calculate current outstanding due
            $previousDue = (float) $customer->sales()->lockForUpdate()->sum('due_amount');

            if ($previousDue <= 0) {
                throw new \InvalidArgumentException('এই কাস্টমারের কোনো বকেয়া অবশিষ্ট নেই।');
            }

            $amount = (float) $validated['amount'];

            if ($amount > $previousDue) {
                throw new \InvalidArgumentException(
                    'জমার পরিমাণ (৳'.number_format($amount, 2).') কাস্টমারের বর্তমান মোট বকেয়া (৳'.number_format($previousDue, 2).') এর বেশি হতে পারে না।'
                );
            }

            $remainingDue = max(0, $previousDue - $amount);
            $voucherNo = CustomerPayment::generateVoucherNo();

            // Create Customer Payment record
            $payment = CustomerPayment::create([
                'voucher_no' => $voucherNo,
                'customer_id' => $customer->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'previous_due' => $previousDue,
                'remaining_due' => $remainingDue,
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'note' => $validated['note'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Allocate payment FIFO across unpaid sales
            $unpaidSales = $customer->sales()
                ->where('due_amount', '>', 0)
                ->orderBy('sale_date', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $remainingToAllocate = $amount;

            foreach ($unpaidSales as $sale) {
                if ($remainingToAllocate <= 0) {
                    break;
                }

                $saleDue = (float) $sale->due_amount;
                $allocation = min($remainingToAllocate, $saleDue);

                $sale->payments()->create([
                    'customer_payment_id' => $payment->id,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $allocation,
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'note' => "বকেয়া আদায় ভাউচার: {$voucherNo}" . (! empty($validated['note']) ? ' ('.$validated['note'].')' : ''),
                ]);

                $sale->recalculatePayments();
                $remainingToAllocate -= $allocation;
            }

            return $payment;
        });

        // SMS Notification
        $sendSmsRequested = $request->boolean('send_sms', true);
        $smsSent = false;

        if ($sendSmsRequested && ! empty($customerPayment->customer?->mobile)) {
            try {
                $template = Setting::get(
                    'sms_template_payment',
                    'প্রিয় {customer_name}, আপনার ৳{paid_amount} সফলভাবে জমা হয়েছে। পূর্বের বকেয়া: ৳{previous_due}, বর্তমান অবশিষ্ট বকেয়া: ৳{remaining_due}। ভাউচার: {voucher_no}। ধন্যবাদ!'
                );

                $message = $this->smsService->renderCustomerPaymentTemplate(
                    template: $template,
                    customer: $customerPayment->customer,
                    paidAmount: (float) $customerPayment->amount,
                    previousDue: (float) $customerPayment->previous_due,
                    remainingDue: (float) $customerPayment->remaining_due,
                    voucherNo: $customerPayment->voucher_no
                );

                $smsResult = $this->smsService->send(
                    mobile: $customerPayment->customer->mobile,
                    message: $message,
                    type: 'due_payment_receipt',
                    customerId: $customerPayment->customer->id
                );

                $smsSent = $smsResult['success'] ?? false;
            } catch (\Throwable $e) {
                Log::warning('Customer Payment Voucher SMS Dispatch Error: ' . $e->getMessage());
            }
        }

        $successMsg = "বকেয়া টাকা সফলভাবে জমা হয়েছে! ভাউচার নং: {$customerPayment->voucher_no}";
        if ($smsSent) {
            $successMsg .= ' এবং কাস্টমারের মোবাইলে টাকা জমার রসিদ SMS পাঠানো হয়েছে।';
        }

        if (function_exists('toastr')) {
            toastr()->success($successMsg, 'সফল');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'voucher_no' => $customerPayment->voucher_no,
                'redirect_url' => route('admin.customer-payments.show', $customerPayment),
            ]);
        }

        return redirect()->route('admin.customer-payments.show', $customerPayment);
    }

    /**
     * Display the specified money receipt voucher for print & view.
     */
    public function show(CustomerPayment $customerPayment): View
    {
        $customerPayment->load([
            'customer.district',
            'customer.division',
            'customer.zone',
            'salePayments.sale',
            'creator',
        ]);

        $wordsAmount = BanglaNumberHelper::bnWords((float) $customerPayment->amount);

        $siteLogo = Setting::get('site_logo');
        $businessName = Setting::get('business_name', 'সানা ফিশারিজ');
        $businessSubtitle = Setting::get('business_subtitle', 'উন্নত জাতের দেশী-বিদেশী মাছের পোনা উৎপাদক ও পাইকারি বিক্রেতা');
        $proprietorName = Setting::get('proprietor_name', 'আলহাজ্ব নজরুল ইসলাম সানা');
        $proprietorMobile = Setting::get('proprietor_mobile', '01737 908309');
        $operatorName = Setting::get('operator_name', 'মোমিনুর রহমান');
        $operatorMobile = Setting::get('operator_mobile', '01751-722350 NG/BK');
        $businessAddress = Setting::get('business_address', 'নার্সিং পয়েন্ট: দক্ষিণ পিঁজলা গেটের ধার, শ্রীউলা, আশাশুনি, সাতক্ষীরা।');
        $contactNumbers = Setting::get('contact_numbers', '01352 383531, 01352 383532');

        return view('admin.pages.customer-payments.show', compact(
            'customerPayment',
            'wordsAmount',
            'siteLogo',
            'businessName',
            'businessSubtitle',
            'proprietorName',
            'proprietorMobile',
            'operatorName',
            'operatorMobile',
            'businessAddress',
            'contactNumbers'
        ));
    }

    /**
     * Send or resend SMS for the voucher.
     */
    public function sendSms(CustomerPayment $customerPayment): RedirectResponse
    {
        $customerPayment->load('customer');

        if (empty($customerPayment->customer?->mobile)) {
            if (function_exists('toastr')) {
                toastr()->error('কাস্টমারের কোনো মোবাইল নম্বর সংরক্ষিত নেই।', 'ব্যর্থ');
            }
            return back();
        }

        try {
            $template = Setting::get(
                'sms_template_payment',
                'প্রিয় {customer_name}, আপনার ৳{paid_amount} সফলভাবে জমা হয়েছে। পূর্বের বকেয়া: ৳{previous_due}, বর্তমান অবশিষ্ট বকেয়া: ৳{remaining_due}। ভাউচার: {voucher_no}। ধন্যবাদ!'
            );

            $message = $this->smsService->renderCustomerPaymentTemplate(
                template: $template,
                customer: $customerPayment->customer,
                paidAmount: (float) $customerPayment->amount,
                previousDue: (float) $customerPayment->previous_due,
                remainingDue: (float) $customerPayment->remaining_due,
                voucherNo: $customerPayment->voucher_no
            );

            $smsResult = $this->smsService->send(
                mobile: $customerPayment->customer->mobile,
                message: $message,
                type: 'due_payment_receipt',
                customerId: $customerPayment->customer->id
            );

            if ($smsResult['success'] ?? false) {
                if (function_exists('toastr')) {
                    toastr()->success('ভাউচার SMS সফলভাবে পাঠানো হয়েছে!', 'সফল');
                }
            } else {
                if (function_exists('toastr')) {
                    toastr()->warning($smsResult['message'] ?? 'এসএমএস পাঠানো সম্পন্ন হয়নি।', 'সতর্কতা');
                }
            }
        } catch (\Throwable $e) {
            Log::error('Voucher SMS error: ' . $e->getMessage());
            if (function_exists('toastr')) {
                toastr()->error('এসএমএস পাঠানোতে সমস্যা হয়েছে: ' . $e->getMessage(), 'ত্রুটি');
            }
        }

        return back();
    }

    /**
     * Remove the specified payment and recalculate sales.
     */
    public function destroy(CustomerPayment $customerPayment): RedirectResponse
    {
        $voucherNo = $customerPayment->voucher_no;

        DB::transaction(function () use ($customerPayment): void {
            $linkedSalePayments = $customerPayment->salePayments()->with('sale')->get();

            foreach ($linkedSalePayments as $salePayment) {
                $sale = $salePayment->sale;
                $salePayment->delete();
                if ($sale) {
                    $sale->recalculatePayments();
                }
            }

            $customerPayment->delete();
        });

        if (function_exists('toastr')) {
            toastr()->success("মানি রিসিট ভাউচার {$voucherNo} সফলভাবে মুছে ফেলা হয়েছে এবং বকেয়া পুনঃসমন্বয় করা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.customer-payments.index');
    }
}
