<?php

namespace Tests\Feature\Admin;

use App\Models\CashBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashBookTest extends TestCase
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

    public function test_guest_cannot_access_cash_book(): void
    {
        $response = $this->get(route('admin.cash-book.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_cash_book_index(): void
    {
        CashBook::create([
            'type' => 'deposit',
            'category' => 'মূলধন জমা',
            'date' => now()->toDateString(),
            'amount' => 50000,
            'payment_method' => 'নগদ',
            'note' => 'প্রাথমিক মূলধন জমা',
            'created_by' => $this->user->id,
        ]);

        CashBook::create([
            'type' => 'expense',
            'category' => 'লেবার খরচ',
            'date' => now()->toDateString(),
            'amount' => 3000,
            'payment_method' => 'নগদ',
            'note' => 'শ্রমিকদের মজুরি',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.cash-book.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.cash-book.index');
        $response->assertSee('ক্যাশ ও আয়-ব্যয় খাতা');
        $response->assertSee('মূলধন জমা');
        $response->assertSee('লেবার খরচ');
        $response->assertSee('50,000.00');
        $response->assertSee('3,000.00');
        // Net balance: 50000 - 3000 = 47000
        $response->assertSee('47,000.00');
        $response->assertSee('viewCashBookModal');
        $response->assertSee('লেনদেনের পূর্ণাঙ্গ বিবরণ');
    }

    public function test_user_can_record_deposit_entry(): void
    {
        $payload = [
            'type' => 'deposit',
            'category' => 'ব্যাংক থেকে উত্তোলন',
            'date' => now()->toDateString(),
            'amount' => 25000,
            'payment_method' => 'ব্যাংক',
            'reference_no' => 'CHQ-9876',
            'note' => 'চলতি হিসাব থেকে ক্যাশ কাউন্টারে জমা',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.cash-book.store'), $payload);

        $response->assertRedirect(route('admin.cash-book.index'));

        $this->assertDatabaseHas('cash_books', [
            'type' => 'deposit',
            'category' => 'ব্যাংক থেকে উত্তোলন',
            'amount' => '25000.00',
            'payment_method' => 'ব্যাংক',
            'reference_no' => 'CHQ-9876',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_record_expense_entry(): void
    {
        $payload = [
            'type' => 'expense',
            'category' => 'মাছের খাবার ক্রয়',
            'date' => now()->toDateString(),
            'amount' => 12500.50,
            'payment_method' => 'বিকাশ',
            'reference_no' => 'TRX-12345',
            'note' => '১০ বস্তা নার্সিং ফিড ক্রয়',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.cash-book.store'), $payload);

        $response->assertRedirect(route('admin.cash-book.index'));

        $this->assertDatabaseHas('cash_books', [
            'type' => 'expense',
            'category' => 'মাছের খাবার ক্রয়',
            'amount' => '12500.50',
            'payment_method' => 'বিকাশ',
            'reference_no' => 'TRX-12345',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.cash-book.store'), [
            'type' => 'invalid_type',
            'category' => '',
            'date' => 'invalid-date',
            'amount' => -500,
            'payment_method' => '',
        ]);

        $response->assertSessionHasErrors(['type', 'category', 'date', 'amount', 'payment_method']);
    }

    public function test_user_can_update_cash_book_entry(): void
    {
        $entry = CashBook::create([
            'type' => 'expense',
            'category' => 'বিদ্যুৎ বিল',
            'date' => now()->toDateString(),
            'amount' => 4500,
            'payment_method' => 'নগদ',
            'note' => 'প্রাথমিক বিল হিসাব',
            'created_by' => $this->user->id,
        ]);

        $updatePayload = [
            'type' => 'expense',
            'category' => 'বিদ্যুৎ বিল ও জ্বালানি',
            'date' => now()->toDateString(),
            'amount' => 5200,
            'payment_method' => 'বিকাশ',
            'reference_no' => 'BK-5544',
            'note' => 'সংশোধিত বিদ্যুৎ বিল ও জেনারেটর তেল',
        ];

        $response = $this->actingAs($this->user)->put(route('admin.cash-book.update', $entry->id), $updatePayload);

        $response->assertRedirect(route('admin.cash-book.index'));

        $this->assertDatabaseHas('cash_books', [
            'id' => $entry->id,
            'category' => 'বিদ্যুৎ বিল ও জ্বালানি',
            'amount' => '5200.00',
            'payment_method' => 'বিকাশ',
            'reference_no' => 'BK-5544',
        ]);
    }

    public function test_user_can_delete_cash_book_entry(): void
    {
        $entry = CashBook::create([
            'type' => 'deposit',
            'category' => 'ভুল এন্ট্রি',
            'date' => now()->toDateString(),
            'amount' => 1000,
            'payment_method' => 'নগদ',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.cash-book.destroy', $entry->id));

        $response->assertRedirect(route('admin.cash-book.index'));

        $this->assertDatabaseMissing('cash_books', [
            'id' => $entry->id,
        ]);
    }

    public function test_cash_book_filter_by_type_and_category(): void
    {
        CashBook::create([
            'type' => 'deposit',
            'category' => 'নিজস্ব তহবিল জমা',
            'date' => now()->toDateString(),
            'amount' => 10000,
            'payment_method' => 'নগদ',
        ]);

        CashBook::create([
            'type' => 'expense',
            'category' => 'আপ্যায়ন খরচ',
            'date' => now()->toDateString(),
            'amount' => 500,
            'payment_method' => 'নগদ',
        ]);

        // Filter only expenses
        $response = $this->actingAs($this->user)->get(route('admin.cash-book.index', [
            'type' => 'expense',
            'date_preset' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertSee('আপ্যায়ন খরচ');
        $response->assertSee('-৳ 500.00');
        $response->assertDontSee('+৳ 10,000.00');
    }
}
