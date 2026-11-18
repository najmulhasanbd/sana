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

class CustomerDueTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Division $division;
    protected District $district;
    protected Zone $zone;
    protected Customer $customerWithDue;
    protected Customer $customerPaid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->division = Division::create(['name' => 'রাজশাহী বিভাগ', 'status' => true]);
        $this->district = District::create([
            'name' => 'নাটোর জেলা',
            'division_id' => $this->division->id,
            'status' => true,
        ]);
        $this->zone = Zone::create([
            'name' => 'সিংড়া উপজেলা',
            'district_id' => $this->district->id,
            'status' => true,
        ]);

        $this->customerWithDue = Customer::factory()->create([
            'name' => 'মোঃ মোতাহের হোসেন',
            'mobile' => '01712345678',
            'division_id' => $this->division->id,
            'district_id' => $this->district->id,
            'zone_id' => $this->zone->id,
        ]);

        $this->customerPaid = Customer::factory()->create([
            'name' => 'মোঃ ফারুক হোসেন',
            'mobile' => '01898765432',
            'division_id' => $this->division->id,
            'district_id' => $this->district->id,
            'zone_id' => $this->zone->id,
        ]);

        // Sale with due
        Sales::create([
            'invoice_no' => 'SALE-DUE-01',
            'customer_id' => $this->customerWithDue->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 10000,
            'paid_amount' => 4000,
            'due_amount' => 6000,
            'payment_status' => 'partial',
        ]);

        // Fully paid sale
        Sales::create([
            'invoice_no' => 'SALE-PAID-01',
            'customer_id' => $this->customerPaid->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 5000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);
    }

    public function test_guest_cannot_access_customer_due_sheet(): void
    {
        $response = $this->get(route('admin.customer-due.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_customer_due_sheet(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.reports.customer-due');
        $response->assertSee('কাস্টমার বকেয়া শিট (Customer Due Sheet)');
        $response->assertSee('মোঃ মোতাহের হোসেন');
        $response->assertSee('01712345678');
        $response->assertSee('6,000.00');
        // By default, due_only is true, so paid customer should not appear in due list
        $response->assertDontSee('মোঃ ফারুক হোসেন');
    }

    public function test_due_sheet_can_show_all_customers_when_due_only_is_disabled(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', ['due_only' => 0]));

        $response->assertStatus(200);
        $response->assertSee('মোঃ মোতাহের হোসেন');
        $response->assertSee('মোঃ ফারুক হোসেন');
    }

    public function test_due_sheet_filters_by_location(): void
    {
        $otherDivision = Division::create(['name' => 'চট্টগ্রাম বিভাগ', 'status' => true]);
        $otherCustomer = Customer::factory()->create([
            'name' => 'চট্টগ্রামের ক্রেতা',
            'mobile' => '01600112233',
            'division_id' => $otherDivision->id,
        ]);

        Sales::create([
            'invoice_no' => 'SALE-CTG-01',
            'customer_id' => $otherCustomer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 8000,
            'paid_amount' => 2000,
            'due_amount' => 6000,
            'payment_status' => 'partial',
        ]);

        // Filter by Rajshahi division
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', [
            'division_id' => $this->division->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('মোঃ মোতাহের হোসেন');
        $response->assertDontSee('চট্টগ্রামের ক্রেতা');
    }

    public function test_due_sheet_searches_by_name_or_mobile(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', [
            'search' => '01712345678',
        ]));

        $response->assertStatus(200);
        $response->assertSee('মোঃ মোতাহের হোসেন');
    }

    public function test_due_sheet_groups_by_area_properly(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', [
            'group_by_area' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('সিংড়া উপজেলা, নাটোর জেলা, রাজশাহী বিভাগ');
        $response->assertSee('এলাকা বকেয়া:');
    }

    public function test_due_sheet_can_toggle_off_group_by_area(): void
    {
        // When user unchecks group_by_area in the form, filter_applied=1 is sent without group_by_area
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', [
            'filter_applied' => 1,
            'due_only' => 1,
        ]));

        $response->assertStatus(200);
        // Should show flat table instead of grouped area cards
        $response->assertDontSee('এলাকা বকেয়া:');
        $response->assertSee('মোঃ মোতাহের হোসেন');
    }

    public function test_due_sheet_can_toggle_off_due_only(): void
    {
        // When user unchecks due_only, filter_applied=1 is sent without due_only
        $response = $this->actingAs($this->user)->get(route('admin.customer-due.index', [
            'filter_applied' => 1,
            'group_by_area' => 1,
        ]));

        $response->assertStatus(200);
        // Paid customer should now be visible
        $response->assertSee('মোঃ ফারুক হোসেন');
        $response->assertSee('মোঃ মোতাহের হোসেন');
    }
}
