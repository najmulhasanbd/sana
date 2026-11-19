<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
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

    public function test_guest_cannot_access_expenses(): void
    {
        $response = $this->get(route('admin.expense.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_expense_index(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'বিদ্যুৎ বিল',
            'status' => true,
        ]);

        Expense::create([
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 1500,
            'payment_method' => 'Cash',
            'title' => 'পল্লী বিদ্যুৎ বিল',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.expense.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.expense.index');
        $response->assertSee('বিদ্যুৎ বিল');
        $response->assertSee('পল্লী বিদ্যুৎ বিল');
    }

    public function test_can_create_new_expense(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'মাছের খাবার',
            'status' => true,
        ]);

        $payload = [
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 3200,
            'payment_method' => 'Cash',
            'title' => 'নার্সিং ফিড বস্তা',
            'note' => '২ বস্তা কেনা হয়েছে',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.expense.store'), $payload);

        $response->assertRedirect(route('admin.expense.index'));
        $this->assertDatabaseHas('expenses', [
            'expense_category_id' => $category->id,
            'amount' => 3200.00,
            'title' => 'নার্সিং ফিড বস্তা',
        ]);
    }

    public function test_can_update_expense(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'গাড়িভাড়া',
            'status' => true,
        ]);

        $expense = Expense::create([
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 800,
            'payment_method' => 'Cash',
            'title' => 'পিকআপ ভাড়া',
        ]);

        $payload = [
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 1000,
            'payment_method' => 'bKash',
            'title' => 'পিকআপ ভাড়া সংশোধিত',
        ];

        $response = $this->actingAs($this->user)->put(route('admin.expense.update', $expense->id), $payload);

        $response->assertRedirect(route('admin.expense.index'));
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 1000.00,
            'payment_method' => 'bKash',
            'title' => 'পিকআপ ভাড়া সংশোধিত',
        ]);
    }

    public function test_can_delete_expense(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'আপ্যায়ন',
            'status' => true,
        ]);

        $expense = Expense::create([
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 200,
            'payment_method' => 'Cash',
            'title' => 'চা নাস্তা',
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.expense.destroy', $expense->id));

        $response->assertRedirect(route('admin.expense.index'));
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_can_manage_expense_categories(): void
    {
        // Store
        $response = $this->actingAs($this->user)->post(route('admin.expense-category.store'), [
            'name' => 'নতুন বিশেষ খাত',
            'description' => 'টেস্ট বিবরণ',
        ]);
        $response->assertRedirect(route('admin.expense-category.index'));
        $this->assertDatabaseHas('expense_categories', ['name' => 'নতুন বিশেষ খাত']);

        $category = ExpenseCategory::where('name', 'নতুন বিশেষ খাত')->first();

        // Toggle Status
        $this->actingAs($this->user)->patch(route('admin.expense-category.toggle-status', $category->id));
        $this->assertDatabaseHas('expense_categories', ['id' => $category->id, 'status' => false]);

        // Destroy
        $this->actingAs($this->user)->delete(route('admin.expense-category.destroy', $category->id));
        $this->assertDatabaseMissing('expense_categories', ['id' => $category->id]);
    }
}
