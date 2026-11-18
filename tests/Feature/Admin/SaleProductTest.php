<?php

namespace Tests\Feature\Admin;

use App\Models\SaleProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleProductTest extends TestCase
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

    public function test_guest_cannot_access_sale_products(): void
    {
        $response = $this->get(route('admin.sale-product.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_sale_product_index(): void
    {
        SaleProduct::factory()->create(['name' => 'বড় রুই মাছ']);

        $response = $this->actingAs($this->user)->get(route('admin.sale-product.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.sale-product.index');
        $response->assertSee('বড় রুই মাছ');
    }

    public function test_can_search_sale_products(): void
    {
        SaleProduct::factory()->create(['name' => 'কাতলা মাছ']);
        SaleProduct::factory()->create(['name' => 'পাঙ্গাশ মাছ']);

        $response = $this->actingAs($this->user)->get(route('admin.sale-product.index', ['search' => 'কাতলা']));

        $response->assertStatus(200);
        $response->assertSee('কাতলা মাছ');
        $response->assertDontSee('পাঙ্গাশ মাছ');
    }

    public function test_can_create_sale_product(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sale-product.store'), [
            'name' => 'মনোসেক্স তেলাপিয়া কেজি',
        ]);

        $response->assertRedirect(route('admin.sale-product.index'));
        $this->assertDatabaseHas('sale_products', [
            'name' => 'মনোসেক্স তেলাপিয়া কেজি',
        ]);
    }

    public function test_create_sale_product_validation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.sale-product.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_cannot_create_duplicate_sale_product(): void
    {
        SaleProduct::factory()->create(['name' => 'শিং মাছ']);

        $response = $this->actingAs($this->user)->post(route('admin.sale-product.store'), [
            'name' => 'শিং মাছ',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_update_sale_product(): void
    {
        $product = SaleProduct::factory()->create(['name' => 'মাগুর মাছ']);

        $response = $this->actingAs($this->user)->put(route('admin.sale-product.update', $product), [
            'name' => 'দেশি মাগুর মাছ',
        ]);

        $response->assertRedirect(route('admin.sale-product.index'));
        $this->assertDatabaseHas('sale_products', [
            'id' => $product->id,
            'name' => 'দেশি মাগুর মাছ',
        ]);
    }

    public function test_can_delete_sale_product(): void
    {
        $product = SaleProduct::factory()->create(['name' => 'কার্পিও মাছ']);

        $response = $this->actingAs($this->user)->delete(route('admin.sale-product.destroy', $product));

        $response->assertRedirect(route('admin.sale-product.index'));
        $this->assertDatabaseMissing('sale_products', [
            'id' => $product->id,
        ]);
    }
}
