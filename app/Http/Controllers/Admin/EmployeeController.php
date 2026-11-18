<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeSalary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees with statistics and advance balances.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Employee::query()->with(['advances', 'salaries', 'designationRef'])->latest('id');

        if (! empty($search)) {
            $query->search($search);
        }

        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $employees = $query->paginate(15)->withQueryString();

        // Calculate overall stats
        $allEmployees = Employee::with(['advances', 'salaries'])->get();
        $totalAdvanceBalance = $allEmployees->sum(fn ($e) => $e->advance_balance);
        $totalMonthlyPayroll = Employee::where('status', true)->sum('monthly_salary');
        $totalSalaryPaid = (float) EmployeeSalary::sum('net_salary');

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', true)->count(),
            'inactive' => Employee::where('status', false)->count(),
            'monthly_payroll' => $totalMonthlyPayroll,
            'advance_balance' => max(0.0, (float) $totalAdvanceBalance),
            'total_salary_paid' => max(0.0, $totalSalaryPaid),
        ];

        return view('admin.pages.employee.index', compact('employees', 'stats', 'search', 'status'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $designations = Designation::active()->orderBy('name')->get();

        return view('admin.pages.employee.create', compact('designations'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'designation' => ['nullable', 'string', 'max:100'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'nid' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['nullable', 'date'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'কর্মচারীর নাম আবশ্যক।',
            'designation_id.exists' => 'নির্বাচিত পদবী সঠিক নয়।',
            'monthly_salary.required' => 'মাসিক বেতন আবশ্যক।',
            'monthly_salary.numeric' => 'মাসিক বেতন অবশ্যই সংখ্যা হতে হবে।',
            'monthly_salary.min' => 'মাসিক বেতন কখনোই মাইনাস (ঋণাত্মক) হতে পারবে না।',
        ]);

        if (empty($validated['designation_id']) && empty($validated['designation'])) {
            return back()->withInput()->withErrors(['designation_id' => 'পদবী বা কাজের ধরন নির্বাচন করুন।']);
        }

        if (! empty($validated['designation_id'])) {
            $designationModel = Designation::find($validated['designation_id']);
            $validated['designation'] = $designationModel?->name ?? $validated['designation'];
        }

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;
        $validated['monthly_salary'] = max(0.0, (float) $validated['monthly_salary']);
        $validated['joining_date'] = $validated['joining_date'] ?: now()->format('Y-m-d');

        $employee = Employee::create($validated);

        if (function_exists('toastr')) {
            toastr()->success("কর্মচারী '{$employee->name}' সফলভাবে যোগ করা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.employee.index');
    }

    /**
     * Display the specified employee's profile and full financial ledger.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'designationRef',
            'advances' => fn ($q) => $q->latest('date'),
            'salaries' => fn ($q) => $q->latest('payment_date'),
        ]);

        return view('admin.pages.employee.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        $designations = Designation::active()->orderBy('name')->get();

        return view('admin.pages.employee.edit', compact('employee', 'designations'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'designation' => ['nullable', 'string', 'max:100'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'nid' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'joining_date' => ['nullable', 'date'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'কর্মচারীর নাম আবশ্যক।',
            'designation_id.exists' => 'নির্বাচিত পদবী সঠিক নয়।',
            'monthly_salary.required' => 'মাসিক বেতন আবশ্যক।',
            'monthly_salary.numeric' => 'মাসিক বেতন অবশ্যই সংখ্যা হতে হবে।',
            'monthly_salary.min' => 'মাসিক বেতন কখনোই মাইনাস (ঋণাত্মক) হতে পারবে না।',
        ]);

        if (empty($validated['designation_id']) && empty($validated['designation'])) {
            return back()->withInput()->withErrors(['designation_id' => 'পদবী বা কাজের ধরন নির্বাচন করুন।']);
        }

        if (! empty($validated['designation_id'])) {
            $designationModel = Designation::find($validated['designation_id']);
            $validated['designation'] = $designationModel?->name ?? $validated['designation'];
        }

        $validated['status'] = $request->has('status') ? (bool) $request->input('status') : true;
        $validated['monthly_salary'] = max(0.0, (float) $validated['monthly_salary']);

        $employee->update($validated);

        if (function_exists('toastr')) {
            toastr()->success("কর্মচারী '{$employee->name}'-এর তথ্য সফলভাবে আপডেট হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.employee.index');
    }

    /**
     * Toggle active/inactive status of the employee.
     */
    public function toggleStatus(Employee $employee): RedirectResponse
    {
        $employee->update(['status' => ! $employee->status]);

        $statusText = $employee->status ? 'সক্রিয়' : 'নিষ্ক্রিয়';

        if (function_exists('toastr')) {
            toastr()->success("কর্মচারী '{$employee->name}' এখন {$statusText} করা হয়েছে!", 'স্ট্যাটাস আপডেট');
        }

        return back();
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->name;
        $employee->delete();

        if (function_exists('toastr')) {
            toastr()->success("কর্মচারী '{$name}' এবং তার রেকর্ড সফলভাবে মুছে ফেলা হয়েছে!", 'সফল');
        }

        return redirect()->route('admin.employee.index');
    }
}
