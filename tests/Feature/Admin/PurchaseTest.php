<?php

namespace Tests\Feature\Admin;

use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected PurchaseProduct $product1;
    protected PurchaseProduct $product2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->supplier = Supplier::factory()->create([
            'name' => 'মেঘনা হ্যাচারি',
            'mobile' => '01711223344',
        ]);

        $this->product1 = PurchaseProduct::factory()->create(['name' => 'রুই পোনা']);
        $this->product2 = PurchaseProduct::factory()->create(['name' => 'কাতলা পোনা']);
    }

    public function test_guest_cannot_access_purchases(): void
    {
        $response = $this->get(route('admin.purchase.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_purchase_index(): void
    {
        $purchase = Purchase::create([
            'invoice_no' => 'PUR-00001',
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 3000,
            'due_amount' => 2000,
            'payment_status' => 'partial',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.purchase.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.purchase.index');
        $response->assertSee('PUR-00001');
        $response->assertSee('মেঘনা হ্যাচারি');
    }

    public function test_can_view_create_purchase_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.purchase.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.purchase.create');
        $response->assertSee('মেঘনা হ্যাচারি');
        $response->assertSee('রুই পোনা');
    }

    public function test_can_store_purchase_with_items_and_serial_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit_price' => 500, // 5000
                ],
                [
                    'purchase_product_id' => $this->product2->id,
                    'quantity' => 5,
                    'unit_price' => 400, // 2000
                ],
            ],
            'paid_amount' => 4000,
            'payment_method' => 'bKash',
            'transaction_id' => 'TRX998877',
            'note' => 'টেস্ট ক্রয় এন্ট্রি',
        ]);

        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'total_amount' => 7000,
            'paid_amount' => 4000,
            'due_amount' => 3000,
            'payment_status' => 'partial',
        ]);

        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $this->assertStringStartsWith('PUR-', $purchase->invoice_no);
        $this->assertCount(2, $purchase->items);
        $this->assertCount(1, $purchase->payments);

        $this->assertDatabaseHas('purchase_payments', [
            'purchase_id' => $purchase->id,
            'amount' => 4000,
            'payment_method' => 'bKash',
        ]);

        $response->assertRedirect(route('admin.purchase.show', $purchase));
    }

    public function test_store_validation_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase.store'), [
            'supplier_id' => '',
            'purchase_date' => '',
            'items' => [],
        ]);

        $response->assertSessionHasErrors(['supplier_id', 'purchase_date', 'items']);
    }

    public function test_store_validation_fails_with_duplicate_products(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit_price' => 500,
                ],
                [
                    'purchase_product_id' => $this->product1->id, // Duplicate!
                    'quantity' => 5,
                    'unit_price' => 500,
                ],
            ],
            'paid_amount' => 0,
        ]);

        $response->assertSessionHasErrors(['items.0.purchase_product_id', 'items.1.purchase_product_id']);
    }

    public function test_can_view_purchase_show_page(): void
    {
        $purchase = Purchase::create([
            'invoice_no' => 'PUR-00002',
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 2000,
            'paid_amount' => 2000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.purchase.show', $purchase));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.purchase.show');
        $response->assertSee('PUR-00002');
    }

    public function test_can_add_due_payment_and_recalculate_due(): void
    {
        $purchase = Purchase::create([
            'invoice_no' => 'PUR-00003',
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 10000,
            'paid_amount' => 4000,
            'due_amount' => 6000,
            'payment_status' => 'partial',
        ]);

        $purchase->payments()->create([
            'payment_date' => now()->toDateString(),
            'amount' => 4000,
            'payment_method' => 'Cash',
            'note' => 'প্রাথমিক পরিশোধ',
        ]);

        // Add 6000 to fully pay off due
        $response = $this->actingAs($this->user)->post(route('admin.purchase.payment.store', $purchase), [
            'payment_date' => now()->toDateString(),
            'amount' => 6000,
            'payment_method' => 'Nagad',
            'transaction_id' => 'NGD12345',
            'note' => 'বকেয়া সম্পূর্ণ পরিশোধ',
        ]);

        $response->assertSessionHasNoErrors();
        $purchase->refresh();

        $this->assertEquals(10000, (float) $purchase->paid_amount);
        $this->assertEquals(0, (float) $purchase->due_amount);
        $this->assertEquals('paid', $purchase->payment_status);

        $this->assertDatabaseHas('purchase_payments', [
            'purchase_id' => $purchase->id,
            'amount' => 6000,
            'payment_method' => 'Nagad',
        ]);
    }

    public function test_due_payment_validation_fails_if_amount_exceeds_due(): void
    {
        $purchase = Purchase::create([
            'invoice_no' => 'PUR-00004',
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 4000,
            'due_amount' => 1000,
            'payment_status' => 'partial',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.purchase.payment.store', $purchase), [
            'payment_date' => now()->toDateString(),
            'amount' => 2000, // exceeds due 1000
            'payment_method' => 'Cash',
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_can_delete_purchase(): void
    {
        $purchase = Purchase::create([
            'invoice_no' => 'PUR-00005',
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 3000,
            'paid_amount' => 3000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.purchase.destroy', $purchase));

        $response->assertRedirect(route('admin.purchase.index'));
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
    }

    public function test_can_store_purchase_with_box_poly_mir_packaging_calculation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_product_id' => $this->product1->id,
                    'box' => 5,
                    'poly' => 10,
                    'meer' => 2100,
                    'quantity' => 21000,
                    'unit' => 'পোনা',
                    'unit_price' => 370, // 21,000 fry @ 370 Tk/thousand = 7,770 Tk
                ],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'Cash',
            'note' => 'পোনা ক্রয় প্যাকেজিং ক্যালকুলেশন টেস্ট',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'total_amount' => 7770,
            'paid_amount' => 5000,
            'due_amount' => 2770,
            'payment_status' => 'partial',
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_product_id' => $this->product1->id,
            'box' => '5',
            'poly' => '10',
            'meer' => '2100',
            'quantity' => 21000,
            'unit' => 'পোনা',
            'unit_price' => 370,
            'total_price' => 7770,
        ]);

        $purchase = Purchase::where('supplier_id', $this->supplier->id)->latest('id')->first();
        $showResponse = $this->actingAs($this->user)->get(route('admin.purchase.show', $purchase));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('বক্স');
        $showResponse->assertSee('পলি');
        $showResponse->assertSee('মীর');
        $showResponse->assertSee('মোট পোনা');
        $showResponse->assertSee('21,000');
    }
}
