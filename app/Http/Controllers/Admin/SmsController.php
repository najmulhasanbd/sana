<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\Sales;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsController extends Controller
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Display SMS Dashboard & History.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $type = $request->query('type');
        $date = $request->query('date');

        $query = SmsLog::query()->with(['customer', 'sale', 'sender'])->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if (! empty($status)) {
            $query->status($status);
        }

        if (! empty($type)) {
            $query->type($type);
        }

        if (! empty($date)) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => SmsLog::count(),
            'today' => SmsLog::whereDate('created_at', Carbon::today())->count(),
            'sent' => SmsLog::where('status', 'sent')->count(),
            'simulated' => SmsLog::where('status', 'simulated')->count(),
            'failed' => SmsLog::where('status', 'failed')->count(),
        ];

        $settings = $this->smsService->getSettings();
        $customers = Customer::whereNotNull('mobile')->where('mobile', '!=', '')->orderBy('name')->get();
        $divisions = Division::active()->orderBy('name')->get();
        $districts = District::active()->orderBy('name')->get();

        // Due customers count for quick preview
        $dueCustomersCount = Customer::whereHas('sales', function ($q): void {
            $q->where('due_amount', '>', 0);
        })->whereNotNull('mobile')->where('mobile', '!=', '')->count();

        return view('admin.pages.sms.index', compact(
            'logs',
            'stats',
            'settings',
            'customers',
            'divisions',
            'districts',
            'dueCustomersCount',
            'search',
            'status',
            'type',
            'date'
        ));
    }

    /**
     * Send a single SMS to any mobile number or customer.
     */
    public function sendSingle(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string', 'min:11', 'max:15'],
            'message' => ['required', 'string', 'max:1000'],
            'customer_id' => ['nullable', 'exists:customers,id'],
        ], [
            'recipient.required' => 'প্রাপকের মোবাইল নম্বর প্রদান করুন।',
            'recipient.min' => 'সঠিক ১১ ডিজিটের মোবাইল নম্বর লিখুন।',
            'message.required' => 'মেসেজের বিবরণ লিখুন।',
        ]);

        $result = $this->smsService->send(
            mobile: $validated['recipient'],
            message: $validated['message'],
            type: 'single',
            customerId: $validated['customer_id'] ?? null
        );

        if ($result['success']) {
            if (function_exists('toastr')) {
                toastr()->success($result['message'], 'সফল');
            }
        } else {
            if (function_exists('toastr')) {
                toastr()->error($result['message'], 'ব্যর্থ');
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back();
    }

    /**
     * Send personalized Memo SMS for a specific Sale.
     */
    public function sendSaleMemo(Request $request, Sales $sale): JsonResponse|RedirectResponse
    {
        $customer = $sale->customer;
        $mobile = $customer?->mobile;

        if (empty($mobile)) {
            if (function_exists('toastr')) {
                toastr()->error('এই কাস্টমারের কোনো মোবাইল নম্বর যুক্ত নেই!', 'সতর্কতা');
            }
            return back();
        }

        // Custom message from modal if edited, otherwise generated from template
        $template = Setting::get(
            'sms_template_sale',
            'প্রিয় {customer_name}, সানা ফিশারিজ থেকে আপনার মেমো #{invoice_no} তৈরি হয়েছে। মোট বিল: ৳{total_amount}, জমা: ৳{paid_amount}, বকেয়া: ৳{due_amount}। ধন্যবাদ!'
        );

        $customMessage = $request->input('message');
        $message = ! empty($customMessage) ? $customMessage : $this->smsService->renderSaleTemplate($template, $sale);

        $result = $this->smsService->send(
            mobile: $mobile,
            message: $message,
            type: 'invoice_memo',
            customerId: $customer->id,
            salesId: $sale->id
        );

        if ($result['success']) {
            if (function_exists('toastr')) {
                toastr()->success("মেমো #{$sale->invoice_no}-এর হিসাব কাস্টমারের মোবাইলে সফলভাবে পাঠানো হয়েছে!", 'এসএমএস সফল');
            }
        } else {
            if (function_exists('toastr')) {
                toastr()->error($result['message'], 'ব্যর্থ');
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back();
    }

    /**
     * Send personalized Due Reminder SMS to all due customers simultaneously.
     */
    public function sendGroupDue(Request $request): JsonResponse|RedirectResponse
    {
        $customTemplate = $request->input('message');
        $template = ! empty($customTemplate) ? $customTemplate : Setting::get(
            'sms_template_due',
            'জনাব {customer_name}, সানা ফিশারিজে আপনার পূর্বের মোট বকেয়া রয়েছে ৳{due_amount}। দ্রুত পরিশোধের জন্য অনুরোধ করা হলো। ফোন: {company_phone}'
        );

        $divisionId = $request->input('division_id');
        $districtId = $request->input('district_id');

        $query = Customer::query()
            ->withSum('sales as total_due', 'due_amount')
            ->whereNotNull('mobile')
            ->where('mobile', '!=', '');

        if (! empty($divisionId)) {
            $query->where('division_id', $divisionId);
        }

        if (! empty($districtId)) {
            $query->where('district_id', $districtId);
        }

        $dueCustomers = $query->get()->filter(function ($c): bool {
            return (float) ($c->total_due ?? 0) > 0;
        });

        if ($dueCustomers->isEmpty()) {
            if (function_exists('toastr')) {
                toastr()->info('নির্বাচিত এলাকায় কোনো বকেয়া থাকা কাস্টমার পাওয়া যায়নি।', 'তথ্য');
            }
            return back();
        }

        $sentCount = 0;
        foreach ($dueCustomers as $cust) {
            $dueAmount = (float) $cust->total_due;
            $personalizedMessage = $this->smsService->renderCustomerDueTemplate($template, $cust, $dueAmount);

            $this->smsService->send(
                mobile: $cust->mobile,
                message: $personalizedMessage,
                type: 'group_due',
                customerId: $cust->id
            );
            $sentCount++;
        }

        if (function_exists('toastr')) {
            toastr()->success("মোট {$sentCount} জন বকেয়া কাস্টমারকে তাদের নিজ নিজ বকেয়ার হিসাবসহ এসএমএস পাঠানো হয়েছে!", 'বকেয়া তাগাদা সফল');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $sentCount,
                'message' => "মোট {$sentCount} জন বকেয়া কাস্টমারকে এসএমএস পাঠানো হয়েছে!",
            ]);
        }

        return back();
    }

    /**
     * Broadcast notice or announcement SMS to all active customers.
     */
    public function sendBroadcast(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'message.required' => 'প্রচার বা ঘোষণার মেসেজটি লিখুন।',
        ]);

        $customers = Customer::whereNotNull('mobile')
            ->where('mobile', '!=', '')
            ->get();

        if ($customers->isEmpty()) {
            if (function_exists('toastr')) {
                toastr()->warning('ডাটাবেজে কোনো মোবাইল নম্বর যুক্ত কাস্টমার পাওয়া যায়নি।', 'সতর্কতা');
            }
            return back();
        }

        $count = 0;
        foreach ($customers as $cust) {
            $message = $this->smsService->renderBroadcastTemplate($validated['message'], $cust);
            $this->smsService->send(
                mobile: $cust->mobile,
                message: $message,
                type: 'broadcast',
                customerId: $cust->id
            );
            $count++;
        }

        if (function_exists('toastr')) {
            toastr()->success("সকল নিবন্ধিত {$count} জন কাস্টমারকে একই সাথে এসএমএস পাঠানো হয়েছে!", 'সম্প্রচার সম্পন্ন');
        }

        return back();
    }

    /**
     * Test gateway from Settings.
     */
    public function testGateway(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'test_mobile' => ['required', 'string', 'min:11', 'max:15'],
            'test_message' => ['required', 'string', 'max:255'],
        ], [
            'test_mobile.required' => 'টেস্ট মোবাইল নম্বর প্রদান করুন।',
            'test_message.required' => 'টেস্ট মেসেজ লিখুন।',
        ]);

        $result = $this->smsService->send(
            mobile: $validated['test_mobile'],
            message: $validated['test_message'],
            type: 'test'
        );

        if ($result['success']) {
            if (function_exists('toastr')) {
                toastr()->success($result['message'], 'টেস্ট সফল');
            }
        } else {
            if (function_exists('toastr')) {
                toastr()->error($result['message'], 'টেস্ট ব্যর্থ');
            }
        }

        return back();
    }
}
