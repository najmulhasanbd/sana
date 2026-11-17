<?php

namespace Tests\Feature\Admin;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
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

    public function test_guest_cannot_access_suppliers(): void
    {
        $response = $this->get(route('admin.supplier.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_supplier_index(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('admin.supplier.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.supplier.index');
        $response->assertViewHas('suppliers');
        $response->assertViewHas('stats');
    }

    public function test_can_search_suppliers(): void
    {
        Supplier::factory()->create([
            'name' => 'মায়ের দোয়া হ্যাচারি',
            'mobile' => '01711223344',
        ]);
        Supplier::factory()->create([
            'name' => 'যমুনা ফিশারিজ',
            'mobile' => '01899887766',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.supplier.index', ['search' => 'মায়ের দোয়া']));

        $response->assertStatus(200);
        $response->assertSee('মায়ের দোয়া হ্যাচারি');
        $response->assertDontSee('যমুনা ফিশারিজ');
    }

    public function test_can_filter_suppliers_by_status(): void
    {
        Supplier::factory()->create([
            'name' => 'সক্রিয় হ্যাচারি',
            'status' => true,
        ]);
        Supplier::factory()->create([
            'name' => 'নিষ্ক্রিয় হ্যাচারি',
            'status' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.supplier.index', ['status' => 'inactive']));

        $response->assertStatus(200);
        $response->assertSee('নিষ্ক্রিয় হ্যাচারি');
        $response->assertDontSee('সক্রিয় হ্যাচারি');
    }

    public function test_can_view_create_supplier_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.supplier.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.supplier.create');
    }

    public function test_can_store_new_supplier_with_validation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.supplier.store'), [
            'name' => 'মেঘনা ফিশ ফার্ম',
            'mobile' => '01700112233',
            'email' => 'meghna@example.com',
            'address' => 'ত্রিশাল, ময়মনসিংহ',
            'note' => 'উন্নত জাতের রেনু সরবরাহকারী',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.supplier.index'));
        $this->assertDatabaseHas('suppliers', [
            'name' => 'মেঘনা ফিশ ফার্ম',
            'mobile' => '01700112233',
            'email' => 'meghna@example.com',
            'status' => true,
        ]);
    }

    public function test_store_validation_fails_when_required_fields_missing(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.supplier.store'), [
            'name' => '',
            'mobile' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'mobile']);
    }

    public function test_can_view_edit_supplier_page(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.supplier.edit', $supplier));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.supplier.edit');
        $response->assertSee($supplier->name);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'name' => 'পুরাতন নাম',
            'mobile' => '01711111111',
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.supplier.update', $supplier), [
            'name' => 'আপডেটেড নাম',
            'mobile' => '01822222222',
            'email' => 'updated@example.com',
            'address' => 'ঢাকা',
            'note' => 'হালনাগাদ নোট',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.supplier.index'));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'আপডেটেড নাম',
            'mobile' => '01822222222',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_can_toggle_supplier_status(): void
    {
        $supplier = Supplier::factory()->create(['status' => true]);

        $response = $this->actingAs($this->user)->patch(route('admin.supplier.toggle-status', $supplier));

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'status' => false,
        ]);

        // Toggle back to true
        $this->actingAs($this->user)->patch(route('admin.supplier.toggle-status', $supplier));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'status' => true,
        ]);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.supplier.destroy', $supplier));

        $response->assertRedirect(route('admin.supplier.index'));
        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }
}
