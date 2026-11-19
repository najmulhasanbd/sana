<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitLossReportTest extends TestCase
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

    public function test_guest_cannot_access_profit_loss_report(): void
    {
        $response = $this->get(route('admin.report.profit-loss'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profit_loss_report(): void
    {
        $customer = Customer::factory()->create();
        $supplier = Supplier::factory()->create();
        $category = ExpenseCategory::create(['name' => 'অফিস খরচ', 'status' => true]);

        // Sale: 10,000
        Sales::create([
            'invoice_no' => 'SALE-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 10000,
            'paid_amount' => 8000,
            'due_amount' => 2000,
            'payment_status' => 'Partial',
        ]);

        // Purchase: 4,000
        Purchase::create([
            'invoice_no' => 'PUR-001',
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'total_amount' => 4000,
            'paid_amount' => 4000,
            'due_amount' => 0,
            'payment_status' => 'Paid',
        ]);

        // Expense: 1,000
        Expense::create([
            'expense_category_id' => $category->id,
            'date' => now()->toDateString(),
            'amount' => 1000,
            'payment_method' => 'Cash',
            'title' => 'চা নাস্তা ও বিল',
        ]);

        // Net Profit = 10,000 - (4,000 + 1,000) = 5,000
        $response = $this->actingAs($this->user)->get(route('admin.report.profit-loss', ['date_preset' => 'today']));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.reports.profit-loss');
        $response->assertSee('নিট ব্যবসায়িক লাভ');
        $response->assertSee('5,000.00');
    }

    public function test_admin_dashboard_renders_live_statistics(): void
    {
        $customer = Customer::factory()->create();

        Sales::create([
            'invoice_no' => 'SALE-999',
            'customer_id' => $customer->id,
            'sale_date' => now()->toDateString(),
            'total_amount' => 5000,
            'paid_amount' => 3000,
            'due_amount' => 2000,
            'payment_status' => 'Partial',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.index');
        $response->assertSee('মানুষের কাছে মোট বাকি');
        $response->assertSee('2,000.00');
    }
}
