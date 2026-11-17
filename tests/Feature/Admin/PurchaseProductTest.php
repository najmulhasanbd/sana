<?php

namespace Tests\Feature\Admin;

use App\Models\PurchaseProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_purchase_products(): void
    {
        $response = $this->get(route('admin.purchase-product.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_purchase_product_index(): void
    {
        PurchaseProduct::factory()->create(['name' => 'রুই পোনা']);

        $response = $this->actingAs($this->user)->get(route('admin.purchase-product.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.purchase-product.index');
        $response->assertSee('রুই পোনা');
    }

    public function test_can_search_purchase_products(): void
    {
        PurchaseProduct::factory()->create(['name' => 'কাতলা মাছের পোনা']);
        PurchaseProduct::factory()->create(['name' => 'পাঙ্গাশ পোনা']);

        $response = $this->actingAs($this->user)->get(route('admin.purchase-product.index', ['search' => 'কাতলা']));

        $response->assertStatus(200);
        $response->assertSee('কাতলা মাছের পোনা');
        $response->assertDontSee('পাঙ্গাশ পোনা');
    }

    public function test_can_create_purchase_product(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase-product.store'), [
            'name' => 'মনোসেক্স তেলাপিয়া',
        ]);

        $response->assertRedirect(route('admin.purchase-product.index'));
        $this->assertDatabaseHas('purchase_products', [
            'name' => 'মনোসেক্স তেলাপিয়া',
        ]);
    }

    public function test_create_purchase_product_validation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.purchase-product.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_cannot_create_duplicate_purchase_product(): void
    {
        PurchaseProduct::factory()->create(['name' => 'শিং মাছের পোনা']);

        $response = $this->actingAs($this->user)->post(route('admin.purchase-product.store'), [
            'name' => 'শিং মাছের পোনা',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_update_purchase_product(): void
    {
        $product = PurchaseProduct::factory()->create(['name' => 'মাগুর পোনা']);

        $response = $this->actingAs($this->user)->put(route('admin.purchase-product.update', $product), [
            'name' => 'দেশি মাগুর পোনা',
        ]);

        $response->assertRedirect(route('admin.purchase-product.index'));
        $this->assertDatabaseHas('purchase_products', [
            'id' => $product->id,
            'name' => 'দেশি মাগুর পোনা',
        ]);
    }

    public function test_can_delete_purchase_product(): void
    {
        $product = PurchaseProduct::factory()->create(['name' => 'কার্পিও পোনা']);

        $response = $this->actingAs($this->user)->delete(route('admin.purchase-product.destroy', $product));

        $response->assertRedirect(route('admin.purchase-product.index'));
        $this->assertDatabaseMissing('purchase_products', [
            'id' => $product->id,
        ]);
    }
}
