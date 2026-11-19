<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Sales;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Default sandbox SMS settings
        Setting::set('sms_enabled', '1');
        Setting::set('sms_test_mode', '1');
        Setting::set('sms_gateway', 'sandbox');
    }

    public function test_guest_cannot_access_sms_center(): void
    {
        $response = $this->get(route('admin.sms.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_sms_center(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.sms.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.sms.index');
        $response->assertSee('এসএমএস সেন্টার ও নোটিফিকেশন');
    }

    public function test_can_send_single_sms(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'জামাল হোসেন',
            'mobile' => '01711223344',
        ]);

        $payload = [
            'recipient' => '01711223344',
            'message' => 'টেস্ট একক মেসেজ',
            'customer_id' => $customer->id,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.sms.send-single'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('sms_logs', [
            'recipient' => '8801711223344',
            'message' => 'টেস্ট একক মেসেজ',
            'type' => 'single',
            'status' => 'simulated',
        ]);
    }

    public function test_can_send_sale_memo_sms_with_dynamic_calculation(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'আব্দুর রহিম',
            'mobile' => '01812345678',
        ]);

        $sale = Sales::create([
            'invoice_no' => 'SALE-101',
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 15000,
            'paid_amount' => 10000,
            'due_amount' => 5000,
            'payment_status' => 'Partial',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.sales.send-sms', $sale->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('sms_logs', [
            'sales_id' => $sale->id,
            'customer_id' => $customer->id,
            'recipient' => '8801812345678',
            'type' => 'invoice_memo',
            'status' => 'simulated',
        ]);

        $log = SmsLog::where('sales_id', $sale->id)->first();
        $this->assertStringContainsString('SALE-101', $log->message);
        $this->assertStringContainsString('15,000.00', $log->message);
        $this->assertStringContainsString('5,000.00', $log->message);
    }

    public function test_can_send_group_due_reminder_sms(): void
    {
        $custWithDue = Customer::factory()->create([
            'name' => 'বকেয়া কাস্টমার ১',
            'mobile' => '01911122233',
        ]);

        Sales::create([
            'invoice_no' => 'SALE-DUE-1',
            'customer_id' => $custWithDue->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 8000,
            'paid_amount' => 3000,
            'due_amount' => 5000,
            'payment_status' => 'Partial',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.sms.send-group-due'));

        $response->assertRedirect();
        $this->assertDatabaseHas('sms_logs', [
            'customer_id' => $custWithDue->id,
            'type' => 'group_due',
            'status' => 'simulated',
        ]);

        $log = SmsLog::where('customer_id', $custWithDue->id)->where('type', 'group_due')->first();
        $this->assertStringContainsString('5,000.00', $log->message);
    }

    public function test_can_send_broadcast_sms(): void
    {
        Customer::factory()->create(['name' => 'কাস্টমার ক', 'mobile' => '01700000001']);
        Customer::factory()->create(['name' => 'কাস্টমার খ', 'mobile' => '01700000002']);

        $response = $this->actingAs($this->user)->post(route('admin.sms.send-broadcast'), [
            'message' => 'সম্মানিত কাস্টমার, রুই পোনার নতুন চালান এসেছে।',
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, SmsLog::where('type', 'broadcast')->count());
    }

    public function test_can_test_sms_gateway(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sms.test-gateway'), [
            'test_mobile' => '01712345678',
            'test_message' => 'সানা ফিশারিজ টেস্ট মেসেজ',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sms_logs', [
            'recipient' => '8801712345678',
            'message' => 'সানা ফিশারিজ টেস্ট মেসেজ',
            'type' => 'test',
        ]);
    }
}
