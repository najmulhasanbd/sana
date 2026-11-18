<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\District;
use App\Models\Division;
use App\Models\SaleProduct;
use App\Models\Sales;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Division $division;
    protected District $district;
    protected Zone $zone;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->division = Division::create(['name' => 'ঢাকা বিভাগ', 'status' => true]);
        $this->district = District::create([
            'name' => 'গাজীপুর জেলা',
            'division_id' => $this->division->id,
            'status' => true,
        ]);
        $this->zone = Zone::create([
            'name' => 'শ্রীপুর উপজেলা',
            'district_id' => $this->district->id,
            'status' => true,
        ]);

        $this->customer = Customer::factory()->create([
            'name' => 'মোঃ জহিরুল ইসলাম',
            'mobile' => '01711223344',
            'division_id' => $this->division->id,
            'district_id' => $this->district->id,
            'zone_id' => $this->zone->id,
            'address' => 'মাওনা বাজার, শ্রীপুর',
        ]);
    }

    public function test_guest_cannot_access_customers_list(): void
    {
        $response = $this->get(route('admin.customer.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_customers_list(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.customer.index');
        $response->assertSee('কাস্টমার তালিকা ও লেজার');
        $response->assertSee('মোঃ জহিরুল ইসলাম');
        $response->assertSee('01711223344');
    }

    public function test_customer_show_page_displays_ledger_and_invoices(): void
    {
        $product = SaleProduct::factory()->create(['name' => 'রুই পোনা']);

        $sale = Sales::create([
            'invoice_no' => 'SALE-TEST-99',
            'customer_id' => $this->customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 15000,
            'paid_amount' => 5000,
            'due_amount' => 10000,
            'payment_status' => 'partial',
        ]);

        $sale->items()->create([
            'sale_product_id' => $product->id,
            'unit' => 'কেজি',
            'quantity' => 50,
            'unit_price' => 300,
            'total_price' => 15000,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.customer.show', $this->customer));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.customer.show');
        $response->assertSee('মোঃ জহিরুল ইসলাম - লেজার ও হিসাব বিবরণী');
        $response->assertSee('SALE-TEST-99');
        $response->assertSee('15,000.00');
        $response->assertSee('10,000.00');
        $response->assertSee('রুই পোনা');
    }

    public function test_customer_can_be_deleted(): void
    {
        $customerToDelete = Customer::factory()->create([
            'name' => 'মুছে ফেলার কাস্টমার',
            'mobile' => '01999887766',
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.customer.destroy', $customerToDelete));

        $response->assertRedirect(route('admin.customer.index'));
        $this->assertDatabaseMissing('customers', [
            'id' => $customerToDelete->id,
        ]);
    }
}
