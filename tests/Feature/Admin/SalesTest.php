<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\SaleProduct;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;
    protected SaleProduct $product1;
    protected SaleProduct $product2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->customer = Customer::factory()->create([
            'name' => 'মোঃ রফিকুল ইসলাম',
            'mobile' => '01711998877',
        ]);

        $this->product1 = SaleProduct::factory()->create(['name' => 'বড় কাতলা মাছ']);
        $this->product2 = SaleProduct::factory()->create(['name' => 'লাইভ রুই মাছ']);
    }

    public function test_guest_cannot_access_sales(): void
    {
        $response = $this->get(route('admin.sales.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_sales_index(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00001',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 3000,
            'due_amount' => 2000,
            'payment_status' => 'partial',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.sales.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.sales.index');
        $response->assertSee('SALE-00001');
        $response->assertSee('মোঃ রফিকুল ইসলাম');
    }

    public function test_can_view_create_sales_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.sales.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.sales.create');
        $response->assertSee('মোঃ রফিকুল ইসলাম');
        $response->assertSee('বড় কাতলা মাছ');
    }

    public function test_can_store_sale_with_customer_and_items(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sales.store'), [
            'customer_name' => 'আব্দুল করিম',
            'customer_mobile' => '01812345678',
            'customer_address' => 'ফুলপুর, ময়মনসিংহ',
            'sale_date' => now()->toDateString(),
            'items' => [
                [
                    'sale_product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit' => 'কেজি',
                    'unit_price' => 450, // 4500
                ],
                [
                    'sale_product_id' => $this->product2->id,
                    'quantity' => 5,
                    'unit' => 'কেজি',
                    'unit_price' => 400, // 2000
                ],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'Cash',
            'note' => 'টেস্ট বিক্রয় এন্ট্রি',
        ]);

        $this->assertDatabaseHas('customers', [
            'mobile' => '01812345678',
            'name' => 'আব্দুল করিম',
        ]);

        $customer = Customer::where('mobile', '01812345678')->first();
        $this->assertNotNull($customer);

        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'total_amount' => 6500,
            'paid_amount' => 5000,
            'due_amount' => 1500,
            'payment_status' => 'partial',
        ]);

        $sale = Sales::first();
        $this->assertNotNull($sale);
        $this->assertStringStartsWith('SALE-', $sale->invoice_no);
        $this->assertCount(2, $sale->items);
        $this->assertCount(1, $sale->payments);

        $response->assertRedirect(route('admin.sales.show', $sale));
    }

    public function test_store_validation_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sales.store'), [
            'customer_name' => '',
            'customer_mobile' => '',
            'sale_date' => '',
            'items' => [],
        ]);

        $response->assertSessionHasErrors(['customer_name', 'customer_mobile', 'sale_date', 'items']);
    }

    public function test_store_validation_fails_with_duplicate_products(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sales.store'), [
            'customer_name' => 'করিম',
            'customer_mobile' => '01712000000',
            'sale_date' => now()->toDateString(),
            'items' => [
                [
                    'sale_product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit' => 'কেজি',
                    'unit_price' => 500,
                ],
                [
                    'sale_product_id' => $this->product1->id, // Duplicate!
                    'quantity' => 5,
                    'unit' => 'কেজি',
                    'unit_price' => 500,
                ],
            ],
            'paid_amount' => 0,
        ]);

        $response->assertSessionHasErrors(['items.0.sale_product_id', 'items.1.sale_product_id']);
    }

    public function test_can_view_sales_show_invoice_page(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00002',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 3000,
            'paid_amount' => 3000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.sales.show', $sale));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.sales.show');
        $response->assertSee('SALE-00002');
        // All 5 rows (মোট, পূর্ব জের, সর্বমোট, জমা, পাওনা) must appear matching the physical pad
        $response->assertSee('পাওনা:');
        $response->assertSee('পূর্ব জের:');
        $response->assertSee('3,000.00');
    }

    public function test_memo_displays_due_amount_when_due_exists(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-DUE-09',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 2000,
            'due_amount' => 3000,
            'payment_status' => 'partial',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.sales.show', $sale));

        $response->assertStatus(200);
        $response->assertSee('পাওনা:');
        $response->assertSee('3,000.00');
    }

    public function test_can_add_due_payment_and_recalculate_due(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00003',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 10000,
            'paid_amount' => 4000,
            'due_amount' => 6000,
            'payment_status' => 'partial',
        ]);

        $sale->payments()->create([
            'payment_date' => now()->toDateString(),
            'amount' => 4000,
            'payment_method' => 'Cash',
            'note' => 'প্রাথমিক পরিশোধ',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.sales.payment.store', $sale), [
            'payment_date' => now()->toDateString(),
            'amount' => 6000,
            'payment_method' => 'bKash',
            'transaction_id' => 'BK12345',
            'note' => 'বকেয়া সম্পূর্ণ পরিশোধ',
        ]);

        $response->assertSessionHasNoErrors();
        $sale->refresh();

        $this->assertEquals(10000, (float) $sale->paid_amount);
        $this->assertEquals(0, (float) $sale->due_amount);
        $this->assertEquals('paid', $sale->payment_status);

        $this->assertDatabaseHas('sale_payments', [
            'sales_id' => $sale->id,
            'amount' => 6000,
            'payment_method' => 'bKash',
        ]);
    }

    public function test_due_payment_validation_fails_if_amount_exceeds_due(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00004',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 4000,
            'due_amount' => 1000,
            'payment_status' => 'partial',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.sales.payment.store', $sale), [
            'payment_date' => now()->toDateString(),
            'amount' => 2000, // exceeds due 1000
            'payment_method' => 'Cash',
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_can_delete_sale(): void
    {
        $sale = Sales::create([
            'invoice_no' => 'SALE-00005',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 3000,
            'paid_amount' => 3000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.sales.destroy', $sale));

        $response->assertRedirect(route('admin.sales.index'));
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }

    public function test_automatic_sms_is_dispatched_on_invoice_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sales.store'), [
            'customer_name' => 'মোমিনুল ইসলাম',
            'customer_mobile' => '01755112233',
            'sale_date' => now()->toDateString(),
            'items' => [
                [
                    'sale_product_id' => $this->product1->id,
                    'quantity' => 21000,
                    'unit' => 'পোনা',
                    'unit_price' => 370,
                ],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'Cash',
            'send_sms' => '1',
        ]);

        $sale = Sales::whereHas('customer', function ($q) {
            $q->where('mobile', '01755112233');
        })->first();

        $this->assertNotNull($sale);
        $this->assertDatabaseHas('sms_logs', [
            'sales_id' => $sale->id,
            'recipient' => '8801755112233',
            'type' => 'invoice_memo',
        ]);
    }

    public function test_automatic_sms_can_be_skipped_when_toggle_is_disabled(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sales.store'), [
            'customer_name' => 'কামাল হোসেন',
            'customer_mobile' => '01999887766',
            'sale_date' => now()->toDateString(),
            'items' => [
                [
                    'sale_product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit' => 'কেজি',
                    'unit_price' => 500,
                ],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'Cash',
            'send_sms' => '0',
        ]);

        $sale = Sales::whereHas('customer', function ($q) {
            $q->where('mobile', '01999887766');
        })->first();

        $this->assertNotNull($sale);
        $this->assertDatabaseMissing('sms_logs', [
            'sales_id' => $sale->id,
        ]);
    }
}
