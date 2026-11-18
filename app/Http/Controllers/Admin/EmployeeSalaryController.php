<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of all salary payments with filter and stats.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $employeeId = $request->query('employee_id');
        $salaryMonth = $request->query('salary_month', date('Y-m'));

        $query = EmployeeSalary::query()->with('employee')->latest('payment_date');

        if (! empty($employeeId)) {
            $query->where('employee_id', $employeeId);
        }

        if (! empty($salaryMonth)) {
            $query->where('salary_month', $salaryMonth);
        }

        if (! empty($search)) {
            $query->whereHas('employee', function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $salaries = $query->paginate(15)->withQueryString();

        // Calculate statistics for the selected/current month
        $monthQuery = EmployeeSalary::query();
        if (! empty($salaryMonth)) {
            $monthQuery->where('salary_month', $salaryMonth);
        }

        $stats = [
            'total_payments' => (clone $monthQuery)->count(),
            'total_paid' => (float) (clone $monthQuery)->sum('net_salary'),
            'total_deductions' => (float) (clone $monthQuery)->sum('advance_deduction'),
            'total_bonus' => (float) (clone $monthQuery)->sum('bonus_amount'),
        ];

        $employees = Employee::active()->orderBy('name')->get();

        return view('admin.pages.employee.salary.index', compact(
            'salaries',
            'stats',
            'employees',
            'search',
            'employeeId',
            'salaryMonth'
        ));
    }

    /**
     * Show the form for paying a salary.
     */
    public function create(Request $request): View
    {
        $selectedEmployeeId = $request->query('employee_id');
        $employees = Employee::active()->with(['advances', 'salaries'])->orderBy('name')->get();

        // Preload advance balance for quick JS auto-fill
        $employeeData = $employees->mapWithKeys(function ($emp) {
            return [
                $emp->id => [
                    'name' => $emp->name,
                    'designation' => $emp->designation,
                    'monthly_salary' => (float) $emp->monthly_salary,
                    'advance_balance' => (float) $emp->advance_balance,
                ],
            ];
        });

        $currentMonth = date('Y-m');

        return view('admin.pages.employee.salary.create', compact(
            'employees',
            'employeeData',
            'selectedEmployeeId',
            'currentMonth'
        ));
    }

    /**
     * Store a newly created salary payment in storage.
     * Strictly enforces that net salary and advance balance NEVER become negative.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'salary_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'payment_date' => ['required', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'bonus_amount' => ['nullable', 'numeric', 'min:0'],
            'advance_deduction' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'employee_id.required' => 'কর্মচারী নির্বাচন করুন।',
            'salary_month.required' => 'বেতনের মাস নির্বাচন করুন।',
            'payment_date.required' => 'পরিশোধের তারিখ আবশ্যক।',
            'base_salary.required' => 'মূল বেতন আবশ্যক।',
            'base_salary.min' => 'মূল বেতন ০ এর কম হতে পারে না।',
            'bonus_amount.min' => 'বোনাস ০ এর কম হতে পারে না।',
            'advance_deduction.min' => 'অগ্রিম কর্তন ০ এর কম হতে পারে না।',
            'payment_method.required' => 'পরিশোধের মাধ্যম নির্বাচন করুন।',
        ]);

        $employee = Employee::with(['advances', 'salaries'])->findOrFail($validated['employee_id']);

        $baseSalary = max(0.0, (float) $validated['base_salary']);
        $bonusAmount = max(0.0, (float) ($validated['bonus_amount'] ?? 0));
        $grossSalary = $baseSalary + $bonusAmount;

        // Current outstanding advance balance of this employee
        $currentAdvanceBalance = (float) $employee->advance_balance;

        // SAFE CLAMP (Jeno minus - na hoi):
        // Advance deduction cannot exceed the employee's current advance balance,
        // and cannot exceed gross earnings this month.
        $maxAllowedDeduction = min($currentAdvanceBalance, $grossSalary);

        $requestedDeduction = max(0.0, (float) ($validated['advance_deduction'] ?? 0));
        $finalAdvanceDeduction = min($requestedDeduction, $maxAllowedDeduction);

        // Net salary is guaranteed >= 0 (never negative)
        $netSalary = max(0.0, $grossSalary - $finalAdvanceDeduction);

        $salary = EmployeeSalary::create([
            'employee_id' => $employee->id,
            'salary_month' => $validated['salary_month'],
            'payment_date' => $validated['payment_date'],
            'base_salary' => $baseSalary,
            'bonus_amount' => $bonusAmount,
            'advance_deduction' => $finalAdvanceDeduction,
            'net_salary' => $netSalary,
            'payment_method' => $validated['payment_method'],
            'note' => $validated['note'] ?? null,
        ]);

        if (function_exists('toastr')) {
            $formattedNet = number_format($netSalary, 2);
            toastr()->success("কর্মচারী '{$employee->name}'-এর {$salary->salary_month_name} মাসের বেতন (৳{$formattedNet}) সফলভাবে পরিশোধিত হয়েছে!", 'বেতন পরিশোধ সফল');
        }

        return redirect()->route('admin.employee-salary.show', $salary);
    }

    /**
     * Display the printable salary voucher / slip (বেতন রসিদ).
     */
    public function show(EmployeeSalary $employeeSalary): View
    {
        $employeeSalary->load('employee');

        return view('admin.pages.employee.salary.payslip', [
            'salary' => $employeeSalary,
        ]);
    }

    /**
     * Remove the specified salary payment record from storage.
     */
    public function destroy(EmployeeSalary $employeeSalary): RedirectResponse
    {
        $employeeName = $employeeSalary->employee?->name ?? 'কর্মচারী';
        $month = $employeeSalary->salary_month;

        $employeeSalary->delete();

        if (function_exists('toastr')) {
            toastr()->success("'{$employeeName}'-এর {$month} মাসের বেতন পরিশোধ রেকর্ডটি বাতিল করা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.employee-salary.index');
    }
}
