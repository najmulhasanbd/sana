<?php

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_display_employee_list(): void
    {
        Employee::create([
            'name' => 'মোমিনুর রহমান',
            'designation' => 'অপারেটর',
            'monthly_salary' => 15000,
            'mobile' => '01751722350',
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.employee.index'));

        $response->assertOk()
            ->assertSee('সকল কর্মচারী ও স্টাফ তালিকা')
            ->assertSee('মোমিনুর রহমান')
            ->assertSee('অপারেটর')
            ->assertSee('15,000.00');
    }

    public function test_can_create_a_new_employee(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.employee.store'), [
            'name' => 'কামাল হোসেন',
            'designation' => 'নার্সিং কর্মী',
            'mobile' => '01811223344',
            'monthly_salary' => 12000,
            'joining_date' => '2026-01-01',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.employee.index'));

        $this->assertDatabaseHas('employees', [
            'name' => 'কামাল হোসেন',
            'designation' => 'নার্সিং কর্মী',
            'monthly_salary' => 12000.00,
            'status' => true,
        ]);
    }

    public function test_can_toggle_employee_status(): void
    {
        $employee = Employee::create([
            'name' => 'রহিম উদ্দিন',
            'designation' => 'ম্যানেজার',
            'monthly_salary' => 20000,
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)->patch(route('admin.employee.toggle-status', $employee));

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => false,
        ]);
    }

    public function test_can_record_employee_advance(): void
    {
        $employee = Employee::create([
            'name' => 'হাবিবুর রহমান',
            'designation' => 'নার্সিং কর্মী',
            'monthly_salary' => 10000,
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.employee-advance.store'), [
            'employee_id' => $employee->id,
            'date' => '2026-11-10',
            'amount' => 3000,
            'payment_method' => 'Cash',
            'note' => 'পারিবারিক খরচের জন্য অগ্রিম',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('employee_advances', [
            'employee_id' => $employee->id,
            'amount' => 3000.00,
            'note' => 'পারিবারিক খরচের জন্য অগ্রিম',
        ]);

        $employee->refresh();
        $this->assertEquals(3000.00, $employee->advance_balance);
        $this->assertGreaterThanOrEqual(0, $employee->advance_balance);
    }

    public function test_advance_amount_cannot_be_zero_or_negative(): void
    {
        $employee = Employee::create([
            'name' => 'হাবিবুর রহমান',
            'designation' => 'নার্সিং কর্মী',
            'monthly_salary' => 10000,
            'status' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.employee-advance.store'), [
            'employee_id' => $employee->id,
            'date' => '2026-11-10',
            'amount' => -500, // Negative amount not allowed
            'payment_method' => 'Cash',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_can_pay_salary_and_deduct_advance_without_negative_balance(): void
    {
        $employee = Employee::create([
            'name' => 'সালাম মিয়া',
            'designation' => 'পোনা প্যাকেট কর্মী',
            'monthly_salary' => 10000,
            'status' => true,
        ]);

        // Employee took 4,000 advance
        EmployeeAdvance::create([
            'employee_id' => $employee->id,
            'date' => '2026-11-05',
            'amount' => 4000,
            'payment_method' => 'Cash',
        ]);

        $this->assertEquals(4000.00, $employee->advance_balance);

        // Pay November salary: Base 10,000, Bonus 1,000, Deduct advance 2,500
        $response = $this->actingAs($this->user)->post(route('admin.employee-salary.store'), [
            'employee_id' => $employee->id,
            'salary_month' => '2026-11',
            'payment_date' => '2026-11-30',
            'base_salary' => 10000,
            'bonus_amount' => 1000,
            'advance_deduction' => 2500,
            'payment_method' => 'Cash',
            'note' => 'নভেম্বর মাসের বেতন',
        ]);

        // Gross = 11,000, Deduct = 2,500, Net = 8,500
        $salary = EmployeeSalary::where('employee_id', $employee->id)->first();
        $this->assertNotNull($salary);
        $this->assertEquals(8500.00, (float) $salary->net_salary);
        $this->assertGreaterThanOrEqual(0, (float) $salary->net_salary);

        // Remaining advance balance: 4,000 - 2,500 = 1,500 (Never negative)
        $employee->refresh();
        $this->assertEquals(1500.00, $employee->advance_balance);
        $this->assertGreaterThanOrEqual(0, $employee->advance_balance);

        $response->assertRedirect(route('admin.employee-salary.show', $salary));
    }

    public function test_salary_and_advance_never_become_negative_even_with_excess_deduction_request(): void
    {
        $employee = Employee::create([
            'name' => 'করিম শেখ',
            'designation' => 'শ্রমিক',
            'monthly_salary' => 8000,
            'status' => true,
        ]);

        // Employee took 12,000 advance (more than 1 month salary)
        EmployeeAdvance::create([
            'employee_id' => $employee->id,
            'date' => '2026-11-02',
            'amount' => 12000,
            'payment_method' => 'Cash',
        ]);

        // Try to deduct 15,000 (which is more than gross salary 8,000)
        // Controller must clamp deduction to 8,000 max, leaving net salary = 0 (NOT negative!)
        $response = $this->actingAs($this->user)->post(route('admin.employee-salary.store'), [
            'employee_id' => $employee->id,
            'salary_month' => '2026-11',
            'payment_date' => '2026-11-30',
            'base_salary' => 8000,
            'bonus_amount' => 0,
            'advance_deduction' => 15000,
            'payment_method' => 'Cash',
        ]);

        $salary = EmployeeSalary::where('employee_id', $employee->id)->first();
        $this->assertNotNull($salary);

        // Net salary must be exactly 0.00, NEVER negative
        $this->assertEquals(0.00, (float) $salary->net_salary);
        $this->assertGreaterThanOrEqual(0, (float) $salary->net_salary);

        // Advance deduction clamped to 8,000
        $this->assertEquals(8000.00, (float) $salary->advance_deduction);

        // Remaining advance balance: 12,000 - 8,000 = 4,000 (strictly positive)
        $employee->refresh();
        $this->assertEquals(4000.00, $employee->advance_balance);
        $this->assertGreaterThanOrEqual(0, $employee->advance_balance);
    }

    public function test_can_view_salary_payslip(): void
    {
        $employee = Employee::create([
            'name' => 'আব্দুর রব',
            'designation' => 'কেয়ারটেকার',
            'monthly_salary' => 10000,
            'status' => true,
        ]);

        $salary = EmployeeSalary::create([
            'employee_id' => $employee->id,
            'salary_month' => '2026-11',
            'payment_date' => '2026-11-30',
            'base_salary' => 10000,
            'bonus_amount' => 500,
            'advance_deduction' => 1000,
            'net_salary' => 9500,
            'payment_method' => 'Cash',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.employee-salary.show', $salary));

        $response->assertOk()
            ->assertSee('বেতন রসিদ')
            ->assertSee('আব্দুর রব')
            ->assertSee('9,500.00')
            ->assertSee('উপার্জন (Earnings)')
            ->assertSee('কর্তন (Deductions)');
    }

    public function test_can_view_employee_profile_and_ledger(): void
    {
        $employee = Employee::create([
            'name' => 'মোমিনুর রহমান',
            'designation' => 'অপারেটর',
            'monthly_salary' => 15000,
            'status' => true,
        ]);

        EmployeeAdvance::create([
            'employee_id' => $employee->id,
            'date' => '2026-11-05',
            'amount' => 2000,
            'payment_method' => 'Cash',
            'note' => 'জরুরি প্রয়োজনে অগ্রিম',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.employee.show', $employee));

        $response->assertOk()
            ->assertSee('কর্মচারীর ব্যক্তিগত হিসাব ও লেজার')
            ->assertSee('মোমিনুর রহমান')
            ->assertSee('2,000.00')
            ->assertSee('জরুরি প্রয়োজনে অগ্রিম');
    }

    public function test_can_manage_designations_and_select_when_creating_employee(): void
    {
        // 1. Create a designation
        $response = $this->actingAs($this->user)->post(route('admin.designation.store'), [
            'name' => 'নার্সিং হেড',
            'description' => 'নার্সিং পয়েন্টের প্রধান ইনচার্জ',
        ]);

        $response->assertRedirect(route('admin.designation.index'));
        $this->assertDatabaseHas('designations', ['name' => 'নার্সিং হেড']);

        $designation = \App\Models\Designation::where('name', 'নার্সিং হেড')->first();

        // 2. Create employee by selecting designation_id
        $empResponse = $this->actingAs($this->user)->post(route('admin.employee.store'), [
            'name' => 'আবুল কাশেম',
            'designation_id' => $designation->id,
            'mobile' => '01711223344',
            'monthly_salary' => 18000,
            'joining_date' => '2026-02-01',
        ]);

        $empResponse->assertRedirect(route('admin.employee.index'));
        $this->assertDatabaseHas('employees', [
            'name' => 'আবুল কাশেম',
            'designation_id' => $designation->id,
            'designation' => 'নার্সিং হেড',
        ]);
    }
}
