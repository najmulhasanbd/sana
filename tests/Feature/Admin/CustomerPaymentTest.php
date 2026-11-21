<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\District;
use App\Models\Division;
use App\Models\SaleProduct;
use App\Models\Sales;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Division $division;
    protected District $district;
    protected Zone $zone;
    protected Customer $customer;
    protected SaleProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->division = Division::create(['name' => 'খুলনা বিভাগ', 'status' => true]);
        $this->district = District::create([
            'name' => 'সাতক্ষীরা জেলা',
            'division_id' => $this->division->id,
            'status' => true,
        ]);
        $this->zone = Zone::create([
            'name' => 'আশাশুনি উপজেলা',
            'district_id' => $this->district->id,
            'status' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'মো: রহিম আলী',
            'mobile' => '01711223344',
            'division_id' => $this->division->id,
            'district_id' => $this->district->id,
            'zone_id' => $this->zone->id,
            'address' => 'গ্রাম: শ্রীউলা',
            'status' => true,
        ]);

        $this->product = SaleProduct::create([
            'name' => 'রুই মাছের পোনা',
            'unit' => 'কেজি',
            'default_price' => 500,
            'status' => true,
        ]);
    }

    public function test_authenticated_user_can_view_customer_payments_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-payments.index'));

        $response->assertOk()
            ->assertViewIs('admin.pages.customer-payments.index')
            ->assertSee('বকেয়া জমার ভাউচার ও রসিদ তালিকা');
    }

    public function test_can_view_create_customer_payment_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-payments.create', [
            'customer_id' => $this->customer->id,
        ]));

        $response->assertOk()
            ->assertViewIs('admin.pages.customer-payments.create')
            ->assertSee($this->customer->name);
    }

    public function test_can_collect_customer_due_and_generates_voucher(): void
    {
        // Create an unpaid sale for the customer: Total 10,000, Paid 2,000, Due 8,000
        $sale = Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 10000,
            'paid_amount' => 2000,
            'due_amount' => 8000,
            'payment_status' => 'partial',
        ]);
        $sale->payments()->create([
            'payment_date' => now()->toDateString(),
            'amount' => 2000,
            'payment_method' => 'Cash',
        ]);

        // Customer pays 5,000 directly without a new purchase
        $response = $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 5000,
            'payment_method' => 'Cash',
            'note' => 'বকেয়া জমা দিয়েছেন',
            'send_sms' => 0,
        ]);

        $customerPayment = CustomerPayment::first();

        $this->assertNotNull($customerPayment);
        $this->assertEquals('MR-00001', $customerPayment->voucher_no);
        $this->assertEquals(5000.0, (float) $customerPayment->amount);
        $this->assertEquals(8000.0, (float) $customerPayment->previous_due);
        $this->assertEquals(3000.0, (float) $customerPayment->remaining_due);

        // Check redirected to voucher view
        $response->assertRedirect(route('admin.customer-payments.show', $customerPayment));

        // Refresh sale: paid should be 7,000, due should be 3,000
        $sale->refresh();
        $this->assertEquals(7000.0, (float) $sale->paid_amount);
        $this->assertEquals(3000.0, (float) $sale->due_amount);
        $this->assertEquals('partial', $sale->payment_status);

        // Customer total due should also be 3,000
        $this->customer->refresh();
        $this->assertEquals(3000.0, $this->customer->total_due_amount);
    }

    public function test_payment_is_allocated_fifo_across_multiple_sales(): void
    {
        // Sale 1 (older): Total 4,000, Paid 0, Due 4,000
        $sale1 = Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->subDays(5)->toDateString(),
            'total_amount' => 4000,
            'paid_amount' => 0,
            'due_amount' => 4000,
            'payment_status' => 'due',
        ]);

        // Sale 2 (newer): Total 6,000, Paid 0, Due 6,000
        $sale2 = Sales::create([
            'invoice_no' => 'SALE-00002',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->subDays(2)->toDateString(),
            'total_amount' => 6000,
            'paid_amount' => 0,
            'due_amount' => 6000,
            'payment_status' => 'due',
        ]);

        // Total due is 10,000. Customer pays 6,000.
        // FIFO: Sale 1 (4,000) should be fully paid (0 due).
        // Sale 2 (6,000) should receive 2,000 (remaining due 4,000).
        $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 6000,
            'payment_method' => 'bKash',
            'transaction_id' => 'TRX998877',
            'send_sms' => 0,
        ]);

        $sale1->refresh();
        $sale2->refresh();

        $this->assertEquals(4000.0, (float) $sale1->paid_amount);
        $this->assertEquals(0.0, (float) $sale1->due_amount);
        $this->assertEquals('paid', $sale1->payment_status);

        $this->assertEquals(2000.0, (float) $sale2->paid_amount);
        $this->assertEquals(4000.0, (float) $sale2->due_amount);
        $this->assertEquals('partial', $sale2->payment_status);

        $this->customer->refresh();
        $this->assertEquals(4000.0, $this->customer->total_due_amount);
    }

    public function test_cannot_pay_amount_exceeding_total_due(): void
    {
        Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 3000,
            'paid_amount' => 0,
            'due_amount' => 3000,
            'payment_status' => 'due',
        ]);

        // Customer owes 3,000, trying to pay 4,000
        $response = $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 4000,
            'payment_method' => 'Cash',
        ]);

        $this->assertDatabaseCount('customer_payments', 0);
    }

    public function test_cannot_pay_when_customer_has_no_due(): void
    {
        // Customer has 0 due
        $response = $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 500,
            'payment_method' => 'Cash',
        ]);

        $this->assertDatabaseCount('customer_payments', 0);
    }

    public function test_can_view_printable_voucher_show_page(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 0,
            'due_amount' => 5000,
            'payment_status' => 'due',
        ]);

        $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 3000,
            'payment_method' => 'Cash',
            'send_sms' => 0,
        ]);

        $payment = CustomerPayment::first();

        $response = $this->actingAs($this->user)->get(route('admin.customer-payments.show', $payment));

        $response->assertOk()
            ->assertViewIs('admin.pages.customer-payments.show')
            ->assertSee($payment->voucher_no)
            ->assertSee($this->customer->name)
            ->assertSee('3,000.00')
            ->assertSee('টাকা জমার রসিদ');
    }

    public function test_deleting_customer_payment_recalculates_sales(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 0,
            'due_amount' => 5000,
            'payment_status' => 'due',
        ]);

        $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 5000,
            'payment_method' => 'Cash',
            'send_sms' => 0,
        ]);

        $payment = CustomerPayment::first();
        $sale->refresh();
        $this->assertEquals(0.0, (float) $sale->due_amount);

        // Delete payment
        $response = $this->actingAs($this->user)->delete(route('admin.customer-payments.destroy', $payment));

        $response->assertRedirect(route('admin.customer-payments.index'));
        $this->assertDatabaseCount('customer_payments', 0);

        // Sale balance should be restored
        $sale->refresh();
        $this->assertEquals(5000.0, (float) $sale->due_amount);
        $this->assertEquals(0.0, (float) $sale->paid_amount);
        $this->assertEquals('due', $sale->payment_status);
    }

    public function test_customer_due_sheet_print_view_displays_village_address(): void
    {
        Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 0,
            'due_amount' => 5000,
            'payment_status' => 'due',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index'));

        $response->assertOk()
            ->assertSee('ঠিকানা (শুধু গ্রাম)')
            ->assertSee($this->customer->name)
            ->assertSee($this->customer->address)
            ->assertSee($this->customer->mobile);
    }

    public function test_customer_show_ledger_displays_direct_payment_vouchers(): void
    {
        Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 0,
            'due_amount' => 5000,
            'payment_status' => 'due',
        ]);

        $this->actingAs($this->user)->post(route('admin.customer-payments.store'), [
            'customer_id' => $this->customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 2000,
            'payment_method' => 'Cash',
            'send_sms' => 0,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.customer.show', $this->customer));

        $response->assertOk()
            ->assertSee('MR-00001')
            ->assertSee('বকেয়া জমার ভাউচার ও রসিদসমূহ');
    }
}
