<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sales;
use App\Models\Setting;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Get all SMS settings with sensible defaults.
     *
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return [
            'sms_enabled' => (bool) Setting::get('sms_enabled', true),
            'sms_test_mode' => (bool) Setting::get('sms_test_mode', true),
            'sms_gateway' => Setting::get('sms_gateway', 'sandbox'),
            'sms_api_key' => Setting::get('sms_api_key', ''),
            'sms_sender_id' => Setting::get('sms_sender_id', ''),
            'sms_api_url' => Setting::get('sms_api_url', ''),
            'sms_template_sale' => Setting::get(
                'sms_template_sale',
                'প্রিয় {customer_name}, সানা ফিশারিজ থেকে আপনার মেমো #{invoice_no} তৈরি হয়েছে। মোট বিল: ৳{total_amount}, জমা: ৳{paid_amount}, বকেয়া: ৳{due_amount}। ধন্যবাদ!'
            ),
            'sms_template_due' => Setting::get(
                'sms_template_due',
                'জনাব {customer_name}, সানা ফিশারিজে আপনার পূর্বের মোট বকেয়া রয়েছে ৳{due_amount}। দ্রুত পরিশোধের জন্য অনুরোধ করা হলো। ফোন: {company_phone}'
            ),
            'sms_template_payment' => Setting::get(
                'sms_template_payment',
                'প্রিয় {customer_name}, আপনার ৳{paid_amount} সফলভাবে জমা হয়েছে। বর্তমান অবশিষ্ট বকেয়া: ৳{due_amount}। সানা ফিশারিজ।'
            ),
            'sms_template_broadcast' => Setting::get(
                'sms_template_broadcast',
                'সম্মানিত কাস্টমার, সানা ফিশারিজে উন্নত জাতের মাছের পোনার নতুন চালান এসেছে। যোগাযোগ: {company_phone}'
            ),
        ];
    }

    /**
     * Format Bangladeshi mobile number cleanly (e.g. 017xxxxxxxx or 88017xxxxxxxx).
     */
    public function formatMobile(string $mobile, bool $withCountryCode = true): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $mobile);

        // Remove leading 88 if already present
        if (str_starts_with($cleaned, '880')) {
            $cleaned = substr($cleaned, 2);
        }

        // Must start with 0
        if (! str_starts_with($cleaned, '0')) {
            $cleaned = '0' . $cleaned;
        }

        if ($withCountryCode) {
            return '88' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send an SMS to a single recipient and log the attempt.
     *
     * @return array{success: bool, status: string, message: string, log_id?: int}
     */
    public function send(
        string $mobile,
        string $message,
        string $type = 'single',
        ?int $customerId = null,
        ?int $salesId = null
    ): array {
        $settings = $this->getSettings();

        if (! $settings['sms_enabled']) {
            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'এসএমএস সার্ভিস বর্তমানে সেটিংসে বন্ধ রয়েছে।',
            ];
        }

        $formattedMobile = $this->formatMobile($mobile);
        $gateway = $settings['sms_gateway'];
        $isTestMode = $settings['sms_test_mode'] || $gateway === 'sandbox';

        // In Test / Sandbox Mode, simulate sending
        if ($isTestMode) {
            $log = SmsLog::create([
                'customer_id' => $customerId,
                'sales_id' => $salesId,
                'recipient' => $formattedMobile,
                'message' => $message,
                'type' => $type,
                'status' => 'simulated',
                'gateway' => 'sandbox',
                'response' => 'Simulated test send. No live SMS units were charged.',
                'sent_by' => Auth::id(),
            ]);

            return [
                'success' => true,
                'status' => 'simulated',
                'message' => 'টেস্ট মোডে এসএমএস সফলভাবে রেকর্ড হয়েছে (কোনো চার্জ হয়নি)।',
                'log_id' => $log->id,
            ];
        }

        // Live Gateway Dispatch
        try {
            $responsePayload = $this->dispatchToGateway($gateway, $formattedMobile, $message, $settings);
            $isSuccess = $responsePayload['success'];
            $status = $isSuccess ? 'sent' : 'failed';

            $log = SmsLog::create([
                'customer_id' => $customerId,
                'sales_id' => $salesId,
                'recipient' => $formattedMobile,
                'message' => $message,
                'type' => $type,
                'status' => $status,
                'gateway' => $gateway,
                'response' => is_string($responsePayload['raw']) ? $responsePayload['raw'] : json_encode($responsePayload['raw']),
                'sent_by' => Auth::id(),
            ]);

            return [
                'success' => $isSuccess,
                'status' => $status,
                'message' => $isSuccess ? 'এসএমএস সফলভাবে পাঠানো হয়েছে!' : 'এসএমএস পাঠাতে ব্যর্থ হয়েছে। গেটওয়ে রেসপন্স চেক করুন।',
                'log_id' => $log->id,
            ];
        } catch (\Throwable $e) {
            Log::error('SMS Dispatch Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            $log = SmsLog::create([
                'customer_id' => $customerId,
                'sales_id' => $salesId,
                'recipient' => $formattedMobile,
                'message' => $message,
                'type' => $type,
                'status' => 'failed',
                'gateway' => $gateway,
                'response' => $e->getMessage(),
                'sent_by' => Auth::id(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'এসএমএস পাঠাতে সমস্যা হয়েছে: ' . $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }

    /**
     * Dispatch SMS to specific provider.
     *
     * @param  array<string, mixed>  $settings
     * @return array{success: bool, raw: mixed}
     */
    protected function dispatchToGateway(string $gateway, string $mobile, string $message, array $settings): array
    {
        $apiKey = $settings['sms_api_key'];
        $senderId = $settings['sms_sender_id'];
        $apiUrl = $settings['sms_api_url'];

        switch ($gateway) {
            case 'greenweb':
                $url = ! empty($apiUrl) ? $apiUrl : 'http://api.greenweb.com.bd/api.php';
                $response = Http::timeout(12)->get($url, [
                    'token' => $apiKey,
                    'to' => $mobile,
                    'message' => $message,
                ]);
                $body = $response->body();
                $success = str_contains(strtolower($body), 'ok') || ! str_contains(strtolower($body), 'error');
                return ['success' => $success, 'raw' => $body];

            case 'mimsms':
                $url = ! empty($apiUrl) ? $apiUrl : 'https://api.mimsms.com/api/v3/send-sms';
                $response = Http::timeout(12)->asJson()->post($url, [
                    'apikey' => $apiKey,
                    'senderid' => $senderId,
                    'number' => $mobile,
                    'message' => $message,
                ]);
                $body = $response->json();
                $success = ($response->status() === 200) && (($body['status'] ?? '') === 'success' || ($body['statusCode'] ?? '') == '200');
                return ['success' => $success, 'raw' => $body];

            case 'elitbuzz':
                $url = ! empty($apiUrl) ? $apiUrl : 'https://portal.elitbuzz-bd.com/smsapi';
                $response = Http::timeout(12)->get($url, [
                    'api_key' => $apiKey,
                    'type' => 'text',
                    'contacts' => $mobile,
                    'senderid' => $senderId,
                    'msg' => $message,
                ]);
                $body = $response->body();
                $success = str_contains(strtolower($body), 'sms submitted') || str_contains(strtolower($body), 'success');
                return ['success' => $success, 'raw' => $body];

            case 'smspoh':
            case 'generic':
            default:
                if (empty($apiUrl)) {
                    throw new \RuntimeException('Generic SMS গেটওয়ের জন্য API URL প্রদান করা আবশ্যক।');
                }
                $response = Http::timeout(12)->post($apiUrl, [
                    'api_key' => $apiKey,
                    'sender' => $senderId,
                    'to' => $mobile,
                    'message' => $message,
                ]);
                return ['success' => $response->successful(), 'raw' => $response->body()];
        }
    }

    /**
     * Replace placeholders for a Sale Memo SMS.
     */
    public function renderSaleTemplate(string $template, Sales $sale): string
    {
        $customerName = $sale->customer?->name ?? 'সম্মানিত কাস্টমার';
        $companyName = Setting::get('business_name', 'সানা ফিশারিজ');
        $companyPhone = Setting::get('mobile', Setting::get('proprietor_mobile', ''));

        $replacements = [
            '{customer_name}' => $customerName,
            '{invoice_no}' => $sale->invoice_no,
            '{sale_date}' => $sale->sale_date ? $sale->sale_date->format('d/m/Y') : date('d/m/Y'),
            '{total_amount}' => number_format((float) $sale->total_amount, 2),
            '{paid_amount}' => number_format((float) $sale->paid_amount, 2),
            '{due_amount}' => number_format((float) $sale->due_amount, 2),
            '{company_name}' => $companyName,
            '{company_phone}' => $companyPhone,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Replace placeholders for a Customer Due Reminder SMS.
     */
    public function renderCustomerDueTemplate(string $template, Customer $customer, float $dueAmount): string
    {
        $companyName = Setting::get('business_name', 'সানা ফিশারিজ');
        $companyPhone = Setting::get('mobile', Setting::get('proprietor_mobile', ''));

        $replacements = [
            '{customer_name}' => $customer->name,
            '{due_amount}' => number_format($dueAmount, 2),
            '{total_due}' => number_format($dueAmount, 2),
            '{company_name}' => $companyName,
            '{company_phone}' => $companyPhone,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Replace placeholders for a Broadcast Notice SMS.
     */
    public function renderBroadcastTemplate(string $template, ?Customer $customer = null): string
    {
        $companyName = Setting::get('business_name', 'সানা ফিশারিজ');
        $companyPhone = Setting::get('mobile', Setting::get('proprietor_mobile', ''));

        $replacements = [
            '{customer_name}' => $customer ? $customer->name : 'সম্মানিত কাস্টমার',
            '{company_name}' => $companyName,
            '{company_phone}' => $companyPhone,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
